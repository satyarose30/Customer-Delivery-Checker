<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\DeliveryScheduleRepositoryInterface;
use Domus\CustomerDeliveryChecker\Api\Data\PincodeCheckResultInterface;
use Domus\CustomerDeliveryChecker\Api\PincodeCheckerInterface;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class PincodeCheckerService implements PincodeCheckerInterface
{
    private const XML_PATH_ENABLED = 'customer_delivery_checker/general/enabled';
    private const XML_PATH_NOT_AVAILABLE_MSG = 'customer_delivery_checker/display/failure_message';
    private const XML_PATH_AVAILABLE_MSG = 'customer_delivery_checker/display/success_message';
    private const XML_PATH_COD_MSG = 'domus/customer_delivery_checker/messages/cod_available';

    private const DAYS_MAP = [
        'monday' => 'Mon',
        'tuesday' => 'Tue',
        'wednesday' => 'Wed',
        'thursday' => 'Thu',
        'friday' => 'Fri',
        'saturday' => 'Sat',
        'sunday' => 'Sun'
    ];

    public function __construct(
        private readonly PincodeFactory $pincodeFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly PincodeCheckResultFactory $resultFactory,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly CustomerSession $customerSession,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly Cart $cart,
        private readonly DeliveryScheduleRepositoryInterface $scheduleRepository
    ) {
    }

    public function check(string $pincode, string $countryId = 'IN'): PincodeCheckResultInterface
    {
        return $this->checkAdvanced(
            $pincode,
            $countryId,
            null,
            null,
            null,
            null,
            null
        );
    }

    public function checkAdvanced(
        string $pincode,
        string $countryId = 'IN',
        ?int $productId = null,
        ?int $categoryId = null,
        ?float $cartWeight = null,
        ?float $cartValue = null,
        ?int $storeId = null
    ): PincodeCheckResultInterface {
        $result = $this->resultFactory->create();

        if (!$this->isEnabled()) {
            $result->setIsAvailable(true);
            $result->setMessage('Delivery checker is disabled.');
            return $result;
        }

        $pincode = trim($pincode);
        if ($pincode === '') {
            $result->setMessage('Please enter a valid pincode.');
            return $result;
        }

        $storeId = $storeId ?? $this->storeManager->getStore()->getId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();

        $cartWeight ??= $this->getCartWeight();
        $cartValue ??= $this->getCartValue();

        $matchingRule = $this->findHighestPriorityRule(
            $pincode,
            $countryId,
            $storeId,
            $customerGroupId,
            $cartWeight,
            $cartValue,
            $productId,
            $categoryId
        );

        if (!$matchingRule) {
            $result->setIsAvailable(false);
            $result->setMessage(
                $this->getConfigValue(self::XML_PATH_NOT_AVAILABLE_MSG)
                ?: 'Sorry, delivery is not available to this pincode.'
            );
            return $result;
        }

        $result->setIsAvailable($matchingRule->getIsDeliverable());
        $result->setIsCodAvailable($matchingRule->getIsCodAvailable());
        
        $result->setEstimatedDeliveryDays($this->getEffectiveDeliveryDays($productId, $matchingRule));
        $result->setShippingCharge($this->getEffectiveShippingCharge($productId, $matchingRule));
        
        $result->setCodCharge($matchingRule->getCodCharge());
        $result->setCity($matchingRule->getCity());
        $result->setState($matchingRule->getState());
        $result->setAreaName($matchingRule->getAreaName());
        $result->setLatitude($matchingRule->getLatitude());
        $result->setLongitude($matchingRule->getLongitude());
        $result->setRuleId($matchingRule->getEntityId());
        $result->setCurrentCartWeight($cartWeight);
        $result->setCurrentCartValue($cartValue);

        $result->setCategoryRestricted(!empty($matchingRule->getCategories()));
        $result->setProductRestricted(!empty($matchingRule->getProducts()));
        $result->setWeightRestricted($matchingRule->getWeightFrom() !== null);
        $result->setPriceRestricted($matchingRule->getPriceFrom() !== null);

        if ($productId !== null) {
            try {
                $product = $this->productRepository->getById($productId);
                $result->setWarranty($product->getAttributeText('warranty') ?: (string)$product->getData('warranty'));
                $result->setReturnable($product->getAttributeText('is_returnable') ?: (string)$product->getData('is_returnable'));
            } catch (\Exception $e) {
            }
        }

        $availableDates = $this->getAvailableDeliveryDates($matchingRule->getEntityId());
        $result->setAvailableDeliveryDates($availableDates);

        if ($matchingRule->getIsDeliverable()) {
            $message = $this->buildAvailableMessage($matchingRule, $result);
            $result->setMessage($message);
        } else {
            $result->setMessage(
                $this->getConfigValue(self::XML_PATH_NOT_AVAILABLE_MSG)
                ?: 'Sorry, delivery is not available to this pincode.'
            );
        }

        return $result;
    }

    private function findHighestPriorityRule(
        string $pincode,
        string $countryId,
        int $storeId,
        int $customerGroupId,
        ?float $cartWeight,
        ?float $cartValue,
        ?int $productId,
        ?int $categoryId
    ): ?Pincode {
        $collection = $this->collectionFactory->create();

        $collection->addActiveFilter()
            ->addPincodeFilter($pincode)
            ->addCountryFilter($countryId)
            ->addStoreFilter($storeId)
            ->addCustomerGroupFilter($customerGroupId)
            ->orderByPriority('DESC');

        if ($cartWeight !== null) {
            $collection->addWeightRangeFilter($cartWeight);
        }

        if ($cartValue !== null) {
            $collection->addPriceRangeFilter($cartValue);
        }

        $rules = $collection->getItems();

        if (empty($rules)) {
            return null;
        }

        foreach ($rules as $rule) {
            if ($this->ruleMatchesProductOrCategory($rule, $productId, $categoryId)) {
                return $rule;
            }
        }

        return null;
    }

    private function ruleMatchesProductOrCategory(Pincode $rule, ?int $productId, ?int $categoryId): bool
    {
        $ruleProducts = $rule->getProducts();
        $ruleCategories = $rule->getCategories();

        if (empty($ruleProducts) && empty($ruleCategories)) {
            return true;
        }

        if (!empty($ruleProducts) && $productId !== null && in_array($productId, $ruleProducts)) {
            return true;
        }

        if (!empty($ruleCategories) && $categoryId !== null && in_array($categoryId, $ruleCategories)) {
            return true;
        }

        if (!empty($ruleCategories) && $productId !== null) {
            $product = $this->productRepository->getById($productId);
            $productCategoryIds = $product->getCategoryIds();

            foreach ($ruleCategories as $ruleCategoryId) {
                if (in_array($ruleCategoryId, $productCategoryIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function getAvailableDeliveryDates(int $pincodeId): array
    {
        $slots = $this->scheduleRepository->getAvailableSlots($pincodeId);
        $dates = [];

        foreach ($slots as $slot) {
            $dayName = strtolower($slot->getDayOfWeek());
            $dayCode = self::DAYS_MAP[$dayName] ?? '';

            if ($dayCode) {
                $dates[] = [
                    'day' => $dayCode,
                    'day_name' => $slot->getDayOfWeek(),
                    'time_from' => $slot->getTimeFrom(),
                    'time_to' => $slot->getTimeTo(),
                    'max_orders' => $slot->getMaxOrders(),
                    'current_orders' => $slot->getCurrentOrders()
                ];
            }
        }

        return $dates;
    }

    private function buildAvailableMessage(Pincode $pincodeModel, PincodeCheckResultInterface $result): string
    {
        $parts = [];
        $parts[] = $this->getConfigValue(self::XML_PATH_AVAILABLE_MSG)
            ?: 'Delivery available to this pincode!';

        if ($pincodeModel->getAreaName()) {
            $parts[] = sprintf('Area: %s', $pincodeModel->getAreaName());
        }
        if ($pincodeModel->getCity()) {
            $parts[] = sprintf('City: %s', $pincodeModel->getCity());
        }
        if ($pincodeModel->getEstimatedDeliveryDays()) {
            $parts[] = sprintf('Estimated delivery: %d-%d days',
                $pincodeModel->getEstimatedDeliveryDays(),
                $pincodeModel->getEstimatedDeliveryDays() + 2
            );
        }
        if ($pincodeModel->getIsCodAvailable()) {
            $codMsg = $this->getConfigValue(self::XML_PATH_COD_MSG)
                ?: 'Cash on Delivery is available.';
            $parts[] = $codMsg;
        }

        if (!empty($result->getAvailableDeliveryDates())) {
            $parts[] = 'Select delivery date at checkout';
        }

        return implode(' | ', $parts);
    }

    private function getCartWeight(): float
    {
        $quote = $this->cart->getQuote();
        return (float)$quote->getWeight();
    }

    private function getCartValue(): float
    {
        $quote = $this->cart->getQuote();
        return (float)$quote->getSubtotal();
    }

    private function isEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_ENABLED);
    }

    private function getConfigValue(string $path): ?string
    {
        $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
        return $value !== null ? (string)$value : null;
    }

    private function getEffectiveDeliveryDays(?int $productId, Pincode $rule): ?int
    {
        if ($productId === null) {
            return $rule->getEstimatedDeliveryDays();
        }

        try {
            $product = $this->productRepository->getById($productId);
            $productDeliveryDays = $product->getData('domus_delivery_days');
            
            if ($productDeliveryDays !== null && $productDeliveryDays > 0) {
                return (int)$productDeliveryDays;
            }
        } catch (\Exception $e) {
        }

        return $rule->getEstimatedDeliveryDays();
    }

    private function getEffectiveShippingCharge(?int $productId, Pincode $rule): ?float
    {
        if ($productId === null) {
            return $rule->getShippingCharge();
        }

        try {
            $product = $this->productRepository->getById($productId);
            $productShippingCharge = $product->getData('domus_shipping_charge');
            
            if ($productShippingCharge !== null && $productShippingCharge > 0) {
                return (float)$productShippingCharge;
            }
        } catch (\Exception $e) {
        }

        return $rule->getShippingCharge();
    }
}
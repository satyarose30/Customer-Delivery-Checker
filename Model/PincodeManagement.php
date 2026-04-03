<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\PincodeManagementInterface;
use Domus\CustomerDeliveryChecker\Api\Data\PincodeCheckResultInterfaceFactory;
use Domus\CustomerDeliveryChecker\Api\Data\PincodeCheckResultInterface;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory as PincodeCollectionFactory;
use Domus\CustomerDeliveryChecker\Model\Cache\RedisPincodeCache;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PincodeManagement
 */
class PincodeManagement implements PincodeManagementInterface
{
    /**
     * @var PincodeCheckResultInterfaceFactory
     */
    private PincodeCheckResultInterfaceFactory $resultFactory;
    
    /**
     * @var PincodeCollectionFactory
     */
    private PincodeCollectionFactory $pincodeCollectionFactory;
    
    /**
     * @var RedisPincodeCache
     */
    private RedisPincodeCache $cache;
    
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * PincodeManagement constructor.
     */
    public function __construct(
        PincodeCheckResultInterfaceFactory $resultFactory,
        PincodeCollectionFactory $pincodeCollectionFactory,
        RedisPincodeCache $cache,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger
    ) {
        $this->resultFactory = $resultFactory;
        $this->pincodeCollectionFactory = $pincodeCollectionFactory;
        $this->cache = $cache;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * Check pincode availability
     *
     * @param string $pincode
     * @param string $countryId
     * @param int|null $productId
     * @param int|null $categoryId
     * @param float|null $cartWeight
     * @param float|null $cartValue
     * @return PincodeCheckResultInterface
     */
    public function checkPincode(
        string $pincode,
        string $countryId = 'IN',
        ?int $productId = null,
        ?int $categoryId = null,
        ?float $cartWeight = null,
        ?float $cartValue = null
    ): PincodeCheckResultInterface {
        /** @var PincodeCheckResultInterface $result */
        $result = $this->resultFactory->create();

        try {
            $cacheKey = $pincode . '_' . $countryId . ($productId ? '_' . $productId : '');
            $cached = $this->cache->getCachedPincodeCheck($cacheKey);
            if ($cached) {
                return $this->setResultFromCache($result, $cached);
            }

            if (!$this->validatePincode($pincode)) {
                $result->setIsAvailable(false);
                $result->setMessage('Invalid pincode format');
                return $result;
            }

            $collection = $this->pincodeCollectionFactory->create();
            $collection->addFieldToFilter('pincode', $pincode)
                       ->addFieldToFilter('is_active', 1);

            if (!$collection->getSize()) {
                $result->setIsAvailable(false);
                $result->setMessage('Delivery not available for this pincode');
                return $result;
            }

            $pincodeData = $collection->getFirstItem();
            
            $result->setIsAvailable((bool) $pincodeData->getIsDeliverable());
            $result->setMessage('Delivery available');
            $result->setEstimatedDeliveryDays((int) $pincodeData->getEstimatedDays());
            $result->setIsCodAvailable((bool) $pincodeData->getCodAvailable());
            $result->setCity($pincodeData->getCity());
            $result->setState($pincodeData->getState());
            $result->setLatitude($pincodeData->getLatitude());
            $result->setLongitude($pincodeData->getLongitude());

            if ($productId) {
                $warrantyData = $this->getWarrantyData($productId);
                $result->setWarranty(json_encode($warrantyData));
            }

            $this->cache->cachePincodeCheck($cacheKey, $result->__toArray());

        } catch (\Exception $e) {
            $this->logger->error('Pincode check error: ' . $e->getMessage());
            $result->setIsAvailable(false);
            $result->setMessage('An error occurred while checking pincode');
        }

        return $result;
    }

    /**
     * Validate pincode format
     *
     * @param string $pincode
     * @return bool
     */
    private function validatePincode(string $pincode): bool
    {
        return preg_match('/^[0-9]{6}$/', $pincode) === 1;
    }

    /**
     * Set result from cached data
     *
     * @param PincodeCheckResultInterface $result
     * @param array $cachedData
     * @return PincodeCheckResultInterface
     */
    private function setResultFromCache(PincodeCheckResultInterface $result, array $cachedData): PincodeCheckResultInterface
    {
        foreach ($cachedData as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($result, $method)) {
                $result->$method($value);
            }
        }
        return $result;
    }

/**
     * Get warranty data for product
     *
     * @param int $productId
     * @return array
     */
    private function getWarrantyData(int $productId): array
    {
        try {
            $product = $this->productRepository->getById($productId);

            return [
                'warranty_type' => $product->getAttributeText('warranty_type'),
                'warranty_duration' => $product->getData('warranty_duration'),
                'warranty_details' => $product->getData('warranty_details')
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function checkMultiplePincodes(array $pincodes, string $countryId = 'IN'): array
    {
        $results = [];
        foreach ($pincodes as $pincode) {
            $results[$pincode] = $this->checkPincode($pincode, null);
        }
        return $results;
    }

    public function getAvailableDeliverySlots(string $pincode, string $countryId = 'IN'): array
    {
        $checkResult = $this->checkPincode($pincode, null);
        if (!$checkResult->getIsDeliverable()) {
            return [];
        }

        $estimatedDays = $checkResult->getEstimatedDays() ?? 3;
        $slots = [];
        $startDate = new \DateTime();

        for ($i = 0; $i < 7; $i++) {
            $date = clone $startDate;
            $date->modify("+{$i} days");

            if ($i < $estimatedDays) {
                continue;
            }

            $slots[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('l'),
                'slots' => [
                    ['id' => 'morning', 'label' => 'Morning (9AM - 12PM)', 'available' => true],
                    ['id' => 'afternoon', 'label' => 'Afternoon (12PM - 5PM)', 'available' => true],
                    ['id' => 'evening', 'label' => 'Evening (5PM - 9PM)', 'available' => true]
                ]
            ];
        }

        return $slots;
    }

    public function isExpressDeliveryAvailable(string $pincode, string $countryId = 'IN'): bool
    {
        $checkResult = $this->checkPincode($pincode, null);
        return $checkResult->getIsDeliverable() && $checkResult->getEstimatedDays() <= 2;
    }

    public function getEstimatedDeliveryDate(
        string $pincode,
        string $countryId = 'IN',
        ?string $deliveryType = null
    ): string {
        $checkResult = $this->checkPincode($pincode, null);

        if (!$checkResult->getIsDeliverable()) {
            return '';
        }

        $days = $checkResult->getEstimatedDays() ?? 5;

        if ($deliveryType === 'express') {
            $days = max(1, $days - 2);
        } elseif ($deliveryType === 'overnight') {
            $days = 1;
        }

        $date = new \DateTime();
        $date->modify("+{$days} days");

        return $date->format('Y-m-d');
    }

    public function searchPincodes(string $query, string $countryId = 'IN', int $limit = 10): array
    {
        $collection = $this->pincodeCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->addFieldToFilter([
            'pincode',
            'city',
            'state'
        ], [
            ['like' => $query . '%'],
            ['like' => '%' . $query . '%'],
            ['like' => '%' . $query . '%']
        ]);

        $collection->getSelect()->limit($limit);

        $results = [];
        foreach ($collection as $pincode) {
            $results[] = [
                'pincode' => $pincode->getPincode(),
                'city' => $pincode->getCity(),
                'state' => $pincode->getState(),
                'display_text' => sprintf('%s - %s, %s', $pincode->getPincode(), $pincode->getCity(), $pincode->getState())
            ];
        }

        return $results;
    }

    public function getPincodeDetails(string $pincode, string $countryId = 'IN'): ?array
    {
        $checkResult = $this->checkPincode($pincode, null);

        if (!$checkResult->getIsDeliverable()) {
            return null;
        }

        return [
            'pincode' => $pincode,
            'country_id' => $countryId,
            'city' => $checkResult->getCity(),
            'state' => $checkResult->getState(),
            'latitude' => $checkResult->getLatitude(),
            'longitude' => $checkResult->getLongitude(),
            'estimated_days' => $checkResult->getEstimatedDays(),
            'cod_available' => $checkResult->getCodAvailable(),
            'express_available' => $this->isExpressDeliveryAvailable($pincode, $countryId)
        ];
    }

    public function isCodAvailable(string $pincode, string $countryId = 'IN'): bool
    {
        $checkResult = $this->checkPincode($pincode, null);
        return $checkResult->getCodAvailable();
    }

    public function getShippingCharges(
        string $pincode,
        string $countryId = 'IN',
        float $orderValue = 0,
        float $weight = 0
    ): array {
        $checkResult = $this->checkPincode($pincode, null);

        if (!$checkResult->getIsDeliverable()) {
            return [
                'available' => false,
                'standard' => null,
                'express' => null,
                'overnight' => null
            ];
        }

        $baseCharge = 0;

        return [
            'available' => true,
            'standard' => $baseCharge,
            'express' => $baseCharge + 99,
            'overnight' => $baseCharge + 199,
            'cod_charge' => $checkResult->getCodAvailable() ? ($orderValue > 5000 ? 50 : 0) : null
        ];
    }
}

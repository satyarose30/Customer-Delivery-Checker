<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Observer;

use Domus\CustomerDeliveryChecker\Model\PincodeCheckerService;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class CheckoutPincodeSyncObserver implements ObserverInterface
{
    private const XML_PATH_ENABLED = 'domus/customer_delivery_checker/general/enabled';
    private const XML_PATH_DISPLAY_CHECKOUT = 'domus/customer_delivery_checker/general/display_on_checkout';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly PincodeCheckerService $pincodeCheckerService
    ) {
    }

    public function execute(Observer $observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress || !$shippingAddress->getPostcode()) {
            return;
        }

        $postcode = $shippingAddress->getPostcode();
        $countryId = $shippingAddress->getCountryId() ?: 'IN';

        $result = $this->pincodeCheckerService->check($postcode, $countryId);

        if (!$result->isAvailable()) {
            $shippingAddress->addData([
                'domus_pincode_available' => 0,
                'domus_pincode_message' => $result->getMessage(),
            ]);
        } else {
            $shippingAddress->addData([
                'domus_pincode_available' => 1,
                'domus_pincode_message' => $result->getMessage(),
            ]);
        }
    }

    private function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)
            && (bool)$this->scopeConfig->getValue(self::XML_PATH_DISPLAY_CHECKOUT, ScopeInterface::SCOPE_STORE);
    }
}

<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ShippingAddressPlugin
{
    private const XML_PATH_ENABLED = 'domus/customer_delivery_checker/general/enabled';
    private const XML_PATH_DISPLAY_CHECKOUT = 'domus/customer_delivery_checker/general/display_on_checkout';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function afterProcess(LayoutProcessor $subject, array $jsLayout): array
    {
        if (!$this->isEnabled()) {
            return $jsLayout;
        }

        $customAttributeCode = 'domus_pincode_status';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$customAttributeCode] = [
            'component' => 'Domus_CustomerDeliveryChecker/js/view/checkout/pincode-status',
            'config' => [
                'template' => 'Domus_CustomerDeliveryChecker/checkout/pincode-status',
                'customScope' => 'shippingAddress',
                'customEntry' => $customAttributeCode,
            ],
            'dataScope' => 'shippingAddress.' . $customAttributeCode,
            'label' => 'Delivery Status',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 250,
            'children' => [],
        ];

        return $jsLayout;
    }

    private function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)
            && (bool)$this->scopeConfig->getValue(self::XML_PATH_DISPLAY_CHECKOUT, ScopeInterface::SCOPE_STORE);
    }
}

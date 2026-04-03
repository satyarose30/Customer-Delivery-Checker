<?php
// app/code/Domus/CustomerDeliveryChecker/Plugin/Checkout/LayoutProcessorPlugin.php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class LayoutProcessorPlugin
 * @package Domus\CustomerDeliveryChecker\Plugin\Checkout
 */
class LayoutProcessorPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * LayoutProcessorPlugin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Process js layout
     *
     * @param LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $subject,
        array $jsLayout
    ): array {
        if (!$this->isEnabled()) {
            return $jsLayout;
        }

        // 1. Remove the custom field if it was added (to ensure clean migration)
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['delivery_pincode'])) {
            unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['delivery_pincode']);
        }

        // 2. Modify the standard 'postcode' field to include our custom validator
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode'])) {
            
            $postcodeField = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode'];
            
            // Add custom validation rule
            if (!isset($postcodeField['validation'])) {
                $postcodeField['validation'] = [];
            }
            $postcodeField['validation']['domus-delivery-validator'] = true;
            
            // Ensure the field triggers a check when changed
            // We'll handle the UI feedback via the sibling component 'domus-pincode-checker'
        }

        return $jsLayout;
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    private function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'customer_delivery_checker/general/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }
}

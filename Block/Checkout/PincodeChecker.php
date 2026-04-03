<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\ScopeInterface;

class PincodeChecker extends Template
{
    private const XML_PATH_ENABLED = 'domus/customer_delivery_checker/general/enabled';
    private const XML_PATH_DISPLAY_CHECKOUT = 'domus/customer_delivery_checker/general/display_on_checkout';

    public function __construct(
        Context $context,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)
            && (bool)$this->scopeConfig->getValue(self::XML_PATH_DISPLAY_CHECKOUT, ScopeInterface::SCOPE_STORE);
    }

    public function getCustomerDefaultPostcode(): ?string
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $defaultBilling = $customer->getDefaultBillingAddress();
            if ($defaultBilling && $defaultBilling->getPostcode()) {
                return $defaultBilling->getPostcode();
            }
        }
        return null;
    }

    public function getDefaultCountry(): string
    {
        return $this->scopeConfig->getValue(
            'domus/customer_delivery_checker/general/default_country',
            ScopeInterface::SCOPE_STORE
        ) ?: 'IN';
    }
}

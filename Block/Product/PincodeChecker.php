<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Block\Product;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class PincodeChecker extends AbstractProduct implements ArgumentInterface
{
    private const XML_PATH_ENABLED = 'domus/customer_delivery_checker/general/enabled';
    private const XML_PATH_DISPLAY_PRODUCT = 'domus/customer_delivery_checker/general/display_on_product_page';
    private const XML_PATH_DEFAULT_COUNTRY = 'domus/customer_delivery_checker/general/default_country';
    private const XML_PATH_PINCODE_LABEL = 'domus/customer_delivery_checker/general/pincode_input_label';
    private const XML_PATH_CHECK_BTN = 'domus/customer_delivery_checker/general/check_button_text';
    private const XML_PATH_CHANGE_BTN = 'domus/customer_delivery_checker/general/change_button_text';
    private const XML_PATH_ENABLE_GEOLOCATION = 'domus/customer_delivery_checker/advanced/enable_geolocation';
    private const XML_PATH_ENABLE_DATE_PICKER = 'domus/customer_delivery_checker/advanced/enable_date_picker';

    public function __construct(
        Context $context,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Registry $registry,
        private readonly StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
    }

    public function isEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_ENABLED)
            && (bool)$this->getConfigValue(self::XML_PATH_DISPLAY_PRODUCT);
    }

    public function getDefaultCountry(): string
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_COUNTRY) ?: 'IN';
    }

    public function getPincodeLabel(): string
    {
        return $this->getConfigValue(self::XML_PATH_PINCODE_LABEL) ?: 'Enter your Pincode';
    }

    public function getCheckButtonText(): string
    {
        return $this->getConfigValue(self::XML_PATH_CHECK_BTN) ?: 'Check';
    }

    public function getChangeButtonText(): string
    {
        return $this->getConfigValue(self::XML_PATH_CHANGE_BTN) ?: 'Change';
    }

    public function getEnableGeolocation(): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_ENABLE_GEOLOCATION);
    }

    public function getEnableDatePicker(): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_ENABLE_DATE_PICKER);
    }

    public function getCheckUrl(): string
    {
        return $this->getUrl('rest/V1/domus/pincode/check', ['_secure' => true]);
    }

    public function getCurrentProduct(): ?Product
    {
        return $this->registry->registry('product');
    }

    public function getCurrentProductId(): ?int
    {
        $product = $this->getCurrentProduct();
        return $product ? $product->getId() : null;
    }

    public function getCurrentCategoryId(): ?int
    {
        $product = $this->getCurrentProduct();
        if (!$product) {
            return null;
        }

        $categoryIds = $product->getCategoryIds();
        return !empty($categoryIds) ? (int)$categoryIds[0] : null;
    }

    public function getLocaleCode(): string
    {
        return $this->storeManager->getStore()->getLocaleCode();
    }

    public function getCurrentCurrencyCode(): string
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }

    private function getConfigValue(string $path): ?string
    {
        $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
        return $value !== null ? (string)$value : null;
    }
}
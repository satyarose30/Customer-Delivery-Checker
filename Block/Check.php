<?php
// app/code/Domus/CustomerDeliveryChecker/Block/Check.php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Registry;

/**
 * Class Check
 * @package Domus\CustomerDeliveryChecker\Block
 */
class Check extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    
    /**
     * @var JsonSerializer
     */
    private JsonSerializer $jsonSerializer;
    
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;
    
    /**
     * @var Registry
     */
    private Registry $registry;
    
    /**
     * XML path for Google Maps API Key
     */
    const XML_PATH_GOOGLE_MAPS_API_KEY = 'customer_delivery_checker/general/google_maps_api_key';
    
    /**
     * XML path for warranty display
     */
    const XML_PATH_SHOW_WARRANTY = 'customer_delivery_checker/general/show_warranty';

    /**
     * Check constructor.
     *
     * @param Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param JsonSerializer $jsonSerializer
     * @param ProductRepositoryInterface $productRepository
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        JsonSerializer $jsonSerializer,
        ProductRepositoryInterface $productRepository,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->jsonSerializer = $jsonSerializer;
        $this->productRepository = $productRepository;
        $this->registry = $registry;
    }

    /**
     * Get Google Maps API Key
     *
     * @return string|null
     */
    public function getGoogleMapsApiKey(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLE_MAPS_API_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Check if warranty display is enabled
     *
     * @return bool
     */
    public function isWarrantyEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_WARRANTY,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get current product ID
     *
     * @return int|null
     */
    public function getCurrentProductId(): ?int
    {
        $product = $this->registry->registry('current_product');
        return $product ? (int)$product->getId() : null;
    }
    
    /**
     * Get warranty check URL
     *
     * @return string
     */
    public function getWarrantyCheckUrl(): string
    {
        return $this->getUrl('customerdeliverychecker/rest/warranty');
    }
    
    /**
     * Get pincodes as JSON
     *
     * @return string
     */
    public function getPincodesJson(): string
    {
        $pincodes = $this->getData('pincodes');
        return $this->jsonSerializer->serialize($pincodes);
    }
    
    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'customer_delivery_checker/general/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }
}

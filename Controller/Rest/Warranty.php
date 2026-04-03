<?php
// app/code/Domus/CustomerDeliveryChecker/Controller/Rest/Warranty.php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Rest;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

/**
 * Class Warranty
 * @package Domus\CustomerDeliveryChecker\Controller\Rest
 */
class Warranty implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;
    
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;
    
    /**
     * @var HttpRequest
     */
    private HttpRequest $request;

    /**
     * Warranty constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param ProductRepositoryInterface $productRepository
     * @param HttpRequest $request
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        ProductRepositoryInterface $productRepository,
        HttpRequest $request
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->request = $request;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        
        try {
            // Get product ID from request
            $productId = $this->request->getParam('product_id');
            
            if (!$productId) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Product ID is required')
                ]);
            }

            // Load product
            $product = $this->productRepository->getById($productId);
            
            if (!$product->getId()) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Product not found')
                ]);
            }
            
            // Get warranty information
            $warrantyData = [
                'warranty_type' => $product->getAttributeText('warranty_type'),
                'warranty_duration' => $product->getData('warranty_duration'),
                'warranty_details' => $product->getData('warranty_details')
            ];
            
            // Check if warranty exists
            $hasWarranty = !empty($warrantyData['warranty_type']) || 
                          !empty($warrantyData['warranty_duration']) || 
                          !empty($warrantyData['warranty_details']);
            
            return $result->setData([
                'success' => true,
                'product_name' => $product->getName(),
                'has_warranty' => $hasWarranty,
                'warranty' => $warrantyData
            ]);
            
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => __('An error occurred while fetching warranty information')
            ]);
        }
    }
}

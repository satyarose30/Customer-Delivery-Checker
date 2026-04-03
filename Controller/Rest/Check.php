<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Rest;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory as PincodeCollectionFactory;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\Collection;
use Domus\CustomerDeliveryChecker\Model\PincodeManagement;
use Domus\CustomerDeliveryChecker\Model\Cache\RedisPincodeCache;
use Psr\Log\LoggerInterface;

/**
 * Class Check
 * @package Domus\CustomerDeliveryChecker\Controller\Rest
 */
class Check implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;
    
    /**
     * @var JsonSerializer
     */
    private JsonSerializer $jsonSerializer;
    
    /**
     * @var PincodeCollectionFactory
     */
    private PincodeCollectionFactory $pincodeCollectionFactory;
    
    /**
     * @var HttpRequest
     */
    private HttpRequest $request;
    
    /**
     * @var PincodeManagement
     */
    private PincodeManagement $pincodeManagement;
    
    /**
     * @var RedisPincodeCache
     */
    private RedisPincodeCache $cache;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Check constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param JsonSerializer $jsonSerializer
     * @param PincodeCollectionFactory $pincodeCollectionFactory
     * @param HttpRequest $request
     * @param PincodeManagement $pincodeManagement
     * @param RedisPincodeCache $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        JsonSerializer $jsonSerializer,
        PincodeCollectionFactory $pincodeCollectionFactory,
        HttpRequest $request,
        PincodeManagement $pincodeManagement,
        RedisPincodeCache $cache,
        LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->pincodeCollectionFactory = $pincodeCollectionFactory;
        $this->request = $request;
        $this->pincodeManagement = $pincodeManagement;
        $this->cache = $cache;
        $this->logger = $logger;
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
            $pincode = $this->request->getParam('pincode');
            $countryId = $this->request->getParam('country_id', 'IN');
            $productId = $this->request->getParam('product_id');
            $categoryId = $this->request->getParam('category_id');
            $cartWeight = $this->request->getParam('cart_weight') ? (float)$this->request->getParam('cart_weight') : null;
            $cartValue = $this->request->getParam('cart_value') ? (float)$this->request->getParam('cart_value') : null;
            
            if (!$pincode) {
                return $result->setData([
                    'success' => false,
                    'message' => 'Pincode is required'
                ]);
            }

            $cacheKey = $pincode . '_' . $countryId . ($productId ? '_' . $productId : '');
            $cached = $this->cache->getCachedPincodeCheck($cacheKey);
            if ($cached) {
                return $result->setData($cached);
            }

            $checkResult = $this->pincodeManagement->checkPincode(
                $pincode,
                $countryId ?: 'IN',
                $productId ? (int)$productId : null,
                $categoryId ? (int)$categoryId : null,
                $cartWeight,
                $cartValue
            );
            
            $responseData = $checkResult->getData();
            $responseData['success'] = $checkResult->isAvailable();
            
            $this->cache->cachePincodeCheck($cacheKey, $responseData);
            
            return $result->setData($responseData);
            
        } catch (\Exception $e) {
            $this->logger->error('Pincode check failed', ['exception' => $e]);
            return $result->setData([
                'success' => false,
                'message' => 'An error occurred while checking pincode'
            ]);
        }
    }
}

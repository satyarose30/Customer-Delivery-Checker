<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Rest;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\JsonFactory;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory as PincodeCollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Express
 * @package Domus\CustomerDeliveryChecker\Controller\Rest
 */
class Express implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;
    
    /**
     * @var HttpRequest
     */
    private HttpRequest $request;
    
    /**
     * @var PincodeCollectionFactory
     */
    private PincodeCollectionFactory $pincodeCollectionFactory;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Express constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param HttpRequest $request
     * @param PincodeCollectionFactory $pincodeCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        HttpRequest $request,
        PincodeCollectionFactory $pincodeCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->pincodeCollectionFactory = $pincodeCollectionFactory;
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
            $pincode = trim((string)$this->request->getParam('pincode'));
            $orderValue = (float)$this->request->getParam('order_value', 0);
            
            if (!$pincode) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Pincode is required')
                ]);
            }

            // Get pincode data with express options
            $collection = $this->pincodeCollectionFactory->create();
            $collection->addFieldToFilter('pincode', $pincode)
                      ->addFieldToFilter('is_active', 1);

            if (!$collection->getSize()) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Pincode not serviceable')
                ]);
            }

            $pincodeData = $collection->getFirstItem();
            $expressOptions = $this->getExpressOptions($pincodeData, $orderValue);

            return $result->setData([
                'success' => true,
                'pincode' => $pincode,
                'standard_delivery' => [
                    'estimated_days' => $pincodeData->getEstimatedDays(),
                    'charge' => 0
                ],
                'express_options' => $expressOptions
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('Express options fetch failed', ['exception' => $e]);
            return $result->setData([
                'success' => false,
                'message' => __('An error occurred while fetching express options')
            ]);
        }
    }

    /**
     * Get express delivery options
     *
     * @param \Domus\CustomerDeliveryChecker\Model\Pincode $pincodeData
     * @param float $orderValue
     * @return array
     */
    private function getExpressOptions($pincodeData, float $orderValue): array
    {
        $options = [];
        $deliveryOptions = $pincodeData->getDeliveryOptions() ? explode(',', $pincodeData->getDeliveryOptions()) : [];
        $currentTime = date('H:i');
        $cutOffTime = $pincodeData->getExpressCutoffTime() ?: '16:00';

        // Express Delivery (24-48 hours)
        if (in_array('express', $deliveryOptions) && $currentTime < $cutOffTime) {
            $expressCharge = $pincodeData->getExpressCharge() ?: $this->calculateExpressCharge($orderValue);
            
            $options[] = [
                'type' => 'express',
                'label' => __('Express Delivery (24-48 hours)'),
                'charge' => $expressCharge,
                'estimated_time' => '24-48 hours',
                'available' => true,
                'cutoff' => $cutOffTime,
                'description' => __('Fast delivery with priority handling')
            ];
        }

        // Overnight Delivery (next day before 10 AM)
        if (in_array('overnight', $deliveryOptions) && $this->isOvernightAvailable($currentTime, $cutOffTime)) {
            $overnightCharge = $pincodeData->getOvernightCharge() ?: $this->calculateOvernightCharge($orderValue);
            
            $options[] = [
                'type' => 'overnight',
                'label' => __('Overnight Delivery (by 10 AM)'),
                'charge' => $overnightCharge,
                'estimated_time' => 'Before 10 AM tomorrow',
                'available' => true,
                'cutoff' => $cutOffTime,
                'description' => __('Guaranteed next morning delivery')
            ];
        }

        // Weekend Express (if available)
        if (in_array('weekend_express', $deliveryOptions)) {
            $options[] = [
                'type' => 'weekend_express',
                'label' => __('Weekend Express'),
                'charge' => $pincodeData->getExpressCharge() * 1.2,
                'estimated_time' => 'Saturday/Sunday delivery',
                'available' => true,
                'description' => __('Special weekend delivery option')
            ];
        }

        return $options;
    }

    /**
     * Calculate express charge based on order value
     *
     * @param float $orderValue
     * @return float
     */
    private function calculateExpressCharge(float $orderValue): float
    {
        // Express pricing logic
        if ($orderValue > 5000) {
            return 0; // Free express for orders above 5000
        } elseif ($orderValue > 2000) {
            return 50; // Reduced charge
        } else {
            return 100; // Standard express charge
        }
    }

    /**
     * Calculate overnight charge
     *
     * @param float $orderValue
     * @return float
     */
    private function calculateOvernightCharge(float $orderValue): float
    {
        // Overnight pricing (higher than express)
        if ($orderValue > 10000) {
            return 100;
        } elseif ($orderValue > 5000) {
            return 150;
        } else {
            return 250;
        }
    }

    /**
     * Check if overnight delivery is available
     *
     * @param string $currentTime
     * @param string $cutOffTime
     * @return bool
     */
    private function isOvernightAvailable(string $currentTime, string $cutOffTime): bool
    {
        // Overnight available only on weekdays before cutoff
        $dayOfWeek = date('N'); // 1 (Monday) to 7 (Sunday)
        
        return $dayOfWeek < 6 && $currentTime < $cutOffTime; // Monday to Friday
    }
}

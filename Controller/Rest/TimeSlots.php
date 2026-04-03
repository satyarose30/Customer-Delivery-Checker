<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Rest;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\JsonFactory;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory as PincodeCollectionFactory;
use Domus\CustomerDeliveryChecker\Model\Source\DeliverySlots;
use Domus\CustomerDeliveryChecker\Model\Prediction\ETACalculator;
use Psr\Log\LoggerInterface;

/**
 * Class TimeSlots
 * @package Domus\CustomerDeliveryChecker\Controller\Rest
 */
class TimeSlots implements HttpGetActionInterface
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
     * @var DeliverySlots
     */
    private DeliverySlots $deliverySlots;
    
    /**
     * @var PincodeCollectionFactory
     */
    private PincodeCollectionFactory $pincodeCollectionFactory;
    
    /**
     * @var ETACalculator
     */
    private ETACalculator $etaCalculator;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * TimeSlots constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param HttpRequest $request
     * @param DeliverySlots $deliverySlots
     * @param PincodeCollectionFactory $pincodeCollectionFactory
     * @param ETACalculator $etaCalculator
     * @param LoggerInterface $logger
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        HttpRequest $request,
        DeliverySlots $deliverySlots,
        PincodeCollectionFactory $pincodeCollectionFactory,
        ETACalculator $etaCalculator,
        LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->deliverySlots = $deliverySlots;
        $this->pincodeCollectionFactory = $pincodeCollectionFactory;
        $this->etaCalculator = $etaCalculator;
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
            $date = (string)$this->request->getParam('date', date('Y-m-d'));
            
            if (!$pincode) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Pincode is required')
                ]);
            }

            // Check if pincode is serviceable
            $collection = $this->pincodeCollectionFactory->create();
            $collection->addFieldToFilter('pincode', $pincode)
                      ->addFieldToFilter('is_active', 1);

            if (!$collection->getSize()) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Pincode not serviceable')
                ]);
            }

            // Get available time slots
            $allSlots = $this->deliverySlots->toOptionArray();
            $timeRanges = $this->deliverySlots->getSlotTimeRanges();
            $availableSlots = [];

            foreach ($allSlots as $slot) {
                $slotValue = $slot['value'];
                $timeRange = $timeRanges[$slotValue] ?? null;
                
                // Check if slot is available for this date
                $isAvailable = $this->isSlotAvailable($slotValue, $date);
                
                if ($isAvailable) {
                    $availableSlots[] = [
                        'value' => $slotValue,
                        'label' => $slot['label'],
                        'time_range' => $timeRange,
                        'estimated_time' => $this->getEstimatedTime($pincode, $slotValue, $date)
                    ];
                }
            }

            return $result->setData([
                'success' => true,
                'date' => $date,
                'pincode' => $pincode,
                'available_slots' => $availableSlots
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('Time slot fetch failed', ['exception' => $e]);
            return $result->setData([
                'success' => false,
                'message' => __('An error occurred while fetching time slots')
            ]);
        }
    }

    /**
     * Check if slot is available
     *
     * @param string $slot
     * @param string $date
     * @return bool
     */
    private function isSlotAvailable(string $slot, string $date): bool
    {
        // Check delivery capacity for the slot
        // Check if it's a holiday
        // Check cutoff time
        
        $currentHour = (int)date('H');
        $cutOffTime = 16; // 4 PM cutoff
        
        if (date('Y-m-d') === $date && $currentHour >= $cutOffTime) {
            // If current time is past cutoff, disable some slots for today
            return in_array($slot, ['evening', 'night', 'early_morning'], true);
        }
        
        return true;
    }

    /**
     * Get estimated delivery time
     *
     * @param string $pincode
     * @param string $slot
     * @param string $date
     * @return string
     */
    private function getEstimatedTime(string $pincode, string $slot, string $date): string
    {
        return $this->etaCalculator->predictETABySlot($pincode, $slot, $date);
    }
}

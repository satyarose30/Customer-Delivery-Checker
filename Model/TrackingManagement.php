<?php
namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\TrackingManagementInterface;
use Domus\CustomerDeliveryChecker\Api\Data\TrackingResultInterfaceFactory;

class TrackingManagement implements TrackingManagementInterface
{
    /**
     * @var TrackingResultInterfaceFactory
     */
    private $trackingResultFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        TrackingResultInterfaceFactory $trackingResultFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->trackingResultFactory = $trackingResultFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritdoc
     */
    public function trackOrder($orderId)
    {
        $result = $this->trackingResultFactory->create();
        
        try {
            $order = $this->orderRepository->get($orderId);
            $result->setStatus($order->getStatus());
            
            if ($order->hasShipments()) {
                $result->setDetails('Your order has been shipped and is currently in transit. We are coordinating with the logistics partner.');
            } else {
                $result->setDetails('Your order is being processed at our warehouse.');
            }

        } catch (\Exception $e) {
            $result->setStatus('Error');
            $result->setDetails('Invalid order tracking requested.');
        }
        
        return $result;
    }
}

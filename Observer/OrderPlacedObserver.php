<?php
namespace Domus\CustomerDeliveryChecker\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class OrderPlacedObserver implements ObserverInterface
{
    /**
     * @var \Domus\CustomerDeliveryChecker\Model\Analytics\DeliveryRevenue
     */
    protected $deliveryRevenueLogger;

    public function __construct(
        \Domus\CustomerDeliveryChecker\Model\Analytics\DeliveryRevenue $deliveryRevenueLogger
    ) {
        $this->deliveryRevenueLogger = $deliveryRevenueLogger;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order && $order->getId()) {
            $shippingMethod = $order->getShippingMethod();
            
            // Minimal example linking express metrics to standard methods
            $deliveryType = 'standard';
            if (strpos((string)$shippingMethod, 'express') !== false) {
                $deliveryType = 'express';
            }
            
            $shippingAmount = $order->getShippingAmount();
            
            $this->deliveryRevenueLogger->logRevenue(
                $deliveryType,
                (float)$shippingAmount,
                $order->getId()
            );
        }
    }
}

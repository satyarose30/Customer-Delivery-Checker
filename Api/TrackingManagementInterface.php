<?php
namespace Domus\CustomerDeliveryChecker\Api;

interface TrackingManagementInterface
{
    /**
     * Get tracking details for an order.
     *
     * @param string $orderId
     * @return \Domus\CustomerDeliveryChecker\Api\Data\TrackingResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function trackOrder($orderId);
}

<?php
namespace Domus\CustomerDeliveryChecker\Api;

interface TimeSlotManagementInterface
{
    /**
     * Get available time slots for a specific pincode.
     *
     * @param string $pincode
     * @return \Domus\CustomerDeliveryChecker\Api\Data\TimeSlotResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAvailableSlots($pincode);
}

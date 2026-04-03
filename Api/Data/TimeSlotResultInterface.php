<?php
namespace Domus\CustomerDeliveryChecker\Api\Data;

interface TimeSlotResultInterface
{
    /**
     * @return bool
     */
    public function getIsAvailable();

    /**
     * @param bool $isAvailable
     * @return $this
     */
    public function setIsAvailable($isAvailable);

    /**
     * @return \Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleInterface[]|null
     */
    public function getAvailableSlots();

    /**
     * @param \Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleInterface[] $slots
     * @return $this
     */
    public function setAvailableSlots(array $slots);
}

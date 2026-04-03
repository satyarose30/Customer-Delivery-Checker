<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\Data\TimeSlotResultInterface;

class TimeSlotResult implements TimeSlotResultInterface
{
    private bool $isAvailable = false;
    private ?array $availableSlots = null;

    /**
     * @return bool
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * @param bool $isAvailable
     * @return $this
     */
    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = (bool)$isAvailable;
        return $this;
    }

    /**
     * @return \Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleInterface[]|null
     */
    public function getAvailableSlots()
    {
        return $this->availableSlots;
    }

    /**
     * @param \Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleInterface[] $slots
     * @return $this
     */
    public function setAvailableSlots(array $slots)
    {
        $this->availableSlots = $slots;
        return $this;
    }
}

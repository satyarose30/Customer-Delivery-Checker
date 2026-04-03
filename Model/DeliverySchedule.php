<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleInterface;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\DeliverySchedule as DeliveryScheduleResource;
use Magento\Framework\Model\AbstractModel;

class DeliverySchedule extends AbstractModel implements DeliveryScheduleInterface
{
    protected function _construct(): void
    {
        $this->_init(DeliveryScheduleResource::class);
    }

    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function getPincodeId(): int
    {
        return (int)$this->getData(self::PINCODE_ID);
    }

    public function setPincodeId(int $pincodeId): DeliveryScheduleInterface
    {
        return $this->setData(self::PINCODE_ID, $pincodeId);
    }

    public function getDayOfWeek(): string
    {
        return $this->getData(self::DAY_OF_WEEK);
    }

    public function setDayOfWeek(string $day): DeliveryScheduleInterface
    {
        return $this->setData(self::DAY_OF_WEEK, $day);
    }

    public function getTimeFrom(): ?string
    {
        return $this->getData(self::TIME_FROM);
    }

    public function setTimeFrom(?string $time): DeliveryScheduleInterface
    {
        return $this->setData(self::TIME_FROM, $time);
    }

    public function getTimeTo(): ?string
    {
        return $this->getData(self::TIME_TO);
    }

    public function setTimeTo(?string $time): DeliveryScheduleInterface
    {
        return $this->setData(self::TIME_TO, $time);
    }

    public function getIsAvailable(): bool
    {
        return (bool)$this->getData(self::IS_AVAILABLE);
    }

    public function setIsAvailable(bool $available): DeliveryScheduleInterface
    {
        return $this->setData(self::IS_AVAILABLE, $available);
    }

    public function getMaxOrders(): ?int
    {
        return $this->getData(self::MAX_ORDERS) ? (int)$this->getData(self::MAX_ORDERS) : null;
    }

    public function setMaxOrders(?int $maxOrders): DeliveryScheduleInterface
    {
        return $this->setData(self::MAX_ORDERS, $maxOrders);
    }

    public function getCurrentOrders(): int
    {
        return (int)$this->getData(self::CURRENT_ORDERS);
    }

    public function setCurrentOrders(int $current): DeliveryScheduleInterface
    {
        return $this->setData(self::CURRENT_ORDERS, $current);
    }
}
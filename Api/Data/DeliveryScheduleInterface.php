<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api\Data;

interface DeliveryScheduleInterface
{
    public const ENTITY_ID = 'entity_id';
    public const PINCODE_ID = 'pincode_id';
    public const DAY_OF_WEEK = 'day_of_week';
    public const TIME_FROM = 'time_from';
    public const TIME_TO = 'time_to';
    public const IS_AVAILABLE = 'is_available';
    public const MAX_ORDERS = 'max_orders';
    public const CURRENT_ORDERS = 'current_orders';

    public function getEntityId();

    public function getPincodeId(): int;

    public function setPincodeId(int $pincodeId): DeliveryScheduleInterface;

    public function getDayOfWeek(): string;

    public function setDayOfWeek(string $day): DeliveryScheduleInterface;

    public function getTimeFrom(): ?string;

    public function setTimeFrom(?string $time): DeliveryScheduleInterface;

    public function getTimeTo(): ?string;

    public function setTimeTo(?string $time): DeliveryScheduleInterface;

    public function getIsAvailable(): bool;

    public function setIsAvailable(bool $available): DeliveryScheduleInterface;

    public function getMaxOrders(): ?int;

    public function setMaxOrders(?int $maxOrders): DeliveryScheduleInterface;

    public function getCurrentOrders(): int;

    public function setCurrentOrders(int $current): DeliveryScheduleInterface;
}
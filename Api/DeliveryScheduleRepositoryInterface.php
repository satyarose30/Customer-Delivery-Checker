<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api;

use Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleInterface;
use Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface DeliveryScheduleRepositoryInterface
{
    public function getById(int $id): DeliveryScheduleInterface;

    public function save(DeliveryScheduleInterface $schedule): DeliveryScheduleInterface;

    public function delete(DeliveryScheduleInterface $schedule): bool;

    public function getList(SearchCriteriaInterface $criteria): DeliveryScheduleSearchResultsInterface;

    public function getByPincodeId(int $pincodeId): array;

    public function getAvailableSlots(int $pincodeId): array;
}
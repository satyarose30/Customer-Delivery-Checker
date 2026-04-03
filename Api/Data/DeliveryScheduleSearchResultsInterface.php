<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface DeliveryScheduleSearchResultsInterface extends SearchResultsInterface
{
    public function getItems(): array;

    public function setItems(array $items): DeliveryScheduleSearchResultsInterface;
}
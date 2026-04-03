<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class DeliveryScheduleSearchResults extends SearchResults implements DeliveryScheduleSearchResultsInterface
{
}


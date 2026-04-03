<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\Data\SearchResultInterface;
use Domus\CustomerDeliveryChecker\Api\Data\PincodeInterface;
use Magento\Framework\Model\AbstractModel;

class SearchResult extends AbstractModel implements SearchResultInterface
{
    public function getItems()
    {
        return $this->getData('items') ?? [];
    }

    public function setItems(array $items)
    {
        /** @var PincodeInterface[] $items */
        return $this->setData('items', $items);
    }

    public function getTotalCount()
    {
        return (int)($this->getData('total_count') ?? 0);
    }

    public function setTotalCount($count)
    {
        return $this->setData('total_count', (int)$count);
    }
}


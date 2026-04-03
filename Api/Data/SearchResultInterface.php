<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api\Data;

interface SearchResultInterface
{
    /**
     * @return \Domus\CustomerDeliveryChecker\Api\Data\PincodeInterface[]
     */
    public function getItems(): array;

    /**
     * @param \Domus\CustomerDeliveryChecker\Api\Data\PincodeInterface[] $items
     * @return $this
     */
    public function setItems(array $items): SearchResultInterface;

    /**
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * @param int $count
     * @return $this
     */
    public function setTotalCount(int $count): SearchResultInterface;
}

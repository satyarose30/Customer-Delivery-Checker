<?php
namespace Domus\CustomerDeliveryChecker\Api\Data;

interface SearchResultInterface
{
    /**
     * @return \Domus\CustomerDeliveryChecker\Api\Data\PincodeInterface[]
     */
    public function getItems();

    /**
     * @param \Domus\CustomerDeliveryChecker\Api\Data\PincodeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @return int
     */
    public function getTotalCount();

    /**
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count);
}

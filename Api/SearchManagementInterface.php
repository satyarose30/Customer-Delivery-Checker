<?php
namespace Domus\CustomerDeliveryChecker\Api;

interface SearchManagementInterface
{
    /**
     * Search pincodes based on a query term.
     *
     * @param string $query
     * @return \Domus\CustomerDeliveryChecker\Api\Data\SearchResultInterface
     */
    public function searchPincodes($query);
}

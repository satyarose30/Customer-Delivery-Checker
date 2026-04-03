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

    /**
     * Autocomplete helper for WebAPI route compatibility.
     *
     * @param string $query
     * @return \Domus\CustomerDeliveryChecker\Api\Data\SearchResultInterface
     */
    public function autocomplete($query);
}

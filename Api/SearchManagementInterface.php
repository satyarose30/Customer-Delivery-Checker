<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api;

use Domus\CustomerDeliveryChecker\Api\Data\SearchResultInterface;

interface SearchManagementInterface
{
    /**
     * Search pincodes based on a query term.
     *
     * @param string $query
     * @return SearchResultInterface
     */
    public function searchPincodes(string $query): SearchResultInterface;

    /**
     * Autocomplete helper for WebAPI route compatibility.
     *
     * @param string $query
     * @return SearchResultInterface
     */
    public function autocomplete(string $query): SearchResultInterface;
    public function searchPincodes($query);

    /**
     * Autocomplete helper for WebAPI route compatibility.
     *
     * @param string $query
     * @return \Domus\CustomerDeliveryChecker\Api\Data\SearchResultInterface
     */
    public function autocomplete($query);
}

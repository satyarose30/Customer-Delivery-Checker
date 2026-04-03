<?php
namespace Domus\CustomerDeliveryChecker\Plugin\Search;

class OpenSearchPlugin
{
    public function afterSearch($subject, $result)
    {
        // Inject nearest pincode availability in search results
        return $result;
    }
}

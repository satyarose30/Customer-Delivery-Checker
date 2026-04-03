<?php
namespace Domus\CustomerDeliveryChecker\Plugin\Checkout;

class DeliverySuggestionPlugin
{
    public function afterGetConfig($subject, $result)
    {
        return $result;
    }
}

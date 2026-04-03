<?php
namespace Domus\CustomerDeliveryChecker\Plugin\Checkout;

class OrderPlacedPlugin
{
    public function afterPlaceOrder($subject, $result)
    {
        return $result;
    }
}

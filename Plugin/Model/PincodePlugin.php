<?php
namespace Domus\CustomerDeliveryChecker\Plugin\Model;

class PincodePlugin
{
    public function beforeSave($subject)
    {
        // Pre-save validation logical hooks for Pincode model
        return [];
    }
}

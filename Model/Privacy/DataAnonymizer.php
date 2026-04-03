<?php
namespace Domus\CustomerDeliveryChecker\Model\Privacy;

class DataAnonymizer
{
    /**
     * Anonymize customer data for analytics
     *
     * @param string $pincode
     * @param string $customerId
     * @return string
     */
    public function hashIdentifier($pincode, $customerId)
    {
        return hash('sha256', $pincode . $customerId . 'salt');
    }
}

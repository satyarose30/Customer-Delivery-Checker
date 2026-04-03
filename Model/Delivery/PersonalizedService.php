<?php
namespace Domus\CustomerDeliveryChecker\Model\Delivery;

class PersonalizedService
{
    /**
     * Get personalized delivery message for user.
     *
     * @param int $customerId
     * @param string $pincode
     * @return string
     */
    public function getPersonalizedMessage($customerId, $pincode)
    {
        return __('Hi there, we have customized delivery options for %1!', $pincode);
    }
}

<?php
namespace Domus\CustomerDeliveryChecker\Model\Integration;

class ThirdPartyLogistics
{
    /**
     * Interface to generic 3PL API service
     *
     * @param string $pincode
     * @param string $serviceCode
     * @return array
     */
    public function checkServiceability($pincode, $serviceCode)
    {
        // TODO: Call third party logistics implementation
        return [
            'is_serviceable' => true
        ];
    }
}

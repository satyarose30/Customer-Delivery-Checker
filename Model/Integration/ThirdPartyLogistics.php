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
        if (!$pincode || !$serviceCode) {
            return [
                'is_serviceable' => false,
                'message' => 'Pincode and service code are required'
            ];
        }

        // Placeholder behavior until provider integration is implemented.
        // Fail-safe false prevents over-promising deliverability.
        return [
            'is_serviceable' => false,
            'message' => 'Third-party logistics provider is not configured'
        ];
    }
}

<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Integration;

use Domus\CustomerDeliveryChecker\Api\LogisticsProviderInterface;

class ThirdPartyLogistics implements LogisticsProviderInterface
{
    public function getCode(): string
    {
        return 'third_party';
    }

    public function getName(): string
    {
        return 'Third-party Logistics';
    }

    public function isAvailable(string $pincode): bool
    {
        $result = $this->checkServiceability($pincode, 'standard');
        return (bool)($result['is_serviceable'] ?? false);
    }

    public function getDeliveryEstimate(string $pincode, float $weight): array
    {
        $result = $this->checkServiceability($pincode, 'standard');
        if (!($result['is_serviceable'] ?? false)) {
            return [
                'status' => 'error',
                'message' => $result['message'] ?? 'Provider unavailable'
            ];
        }

        return [
            'status' => 'success',
            'eta' => date('Y-m-d', strtotime('+4 days')),
            'days' => 4
        ];
    }

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

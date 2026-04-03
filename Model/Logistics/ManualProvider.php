<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Logistics;

use Domus\CustomerDeliveryChecker\Api\LogisticsProviderInterface;

class ManualProvider implements LogisticsProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'manual';
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Manual/Internal';
    }

    /**
     * @inheritdoc
     */
    public function isAvailable(string $pincode): bool
    {
        // By default, if the pincode is checked against internal rules, it's considered available
        // The service layer handles the actual deliverability check against the database
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryEstimate(string $pincode, float $weight): array
    {
        return [
            'status' => 'success',
            'eta' => date('Y-m-d', strtotime('+3 days')),
            'days' => 3
        ];
    }
}

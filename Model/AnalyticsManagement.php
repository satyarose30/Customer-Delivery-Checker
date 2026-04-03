<?php
namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\AnalyticsManagementInterface;

class AnalyticsManagement implements AnalyticsManagementInterface
{
    /**
     * @inheritdoc
     */
    public function getMetrics($startDate, $endDate)
    {
        // TODO: Aggregate metrics over the given time range
        return [
            'total_queries' => 0,
            'successful_deliveries' => 0,
            'express_deliveries' => 0
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRevenueByDeliveryType($startDate, $endDate)
    {
        return [
            'standard' => 0.00,
            'express' => 0.00
        ];
    }
}

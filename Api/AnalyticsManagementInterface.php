<?php
namespace Domus\CustomerDeliveryChecker\Api;

interface AnalyticsManagementInterface
{
    /**
     * Get delivery metrics for a specific time range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getMetrics($startDate, $endDate);

    /**
     * Get revenue generated categorized by delivery types.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getRevenueByDeliveryType($startDate, $endDate);
}

<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\AnalyticsManagementInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class AnalyticsManagement implements AnalyticsManagementInterface
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getMetrics($startDate, $endDate)
    {
        $connection = $this->getConnection();
        $table = $this->resourceConnection->getTableName('domus_delivery_analytics');

        if (!$connection->isTableExists($table)) {
            return [
                'total_queries' => 0,
                'successful_deliveries' => 0,
                'express_deliveries' => 0
            ];
        }

        $select = $connection->select()
            ->from($table, [
                'total_queries' => 'COUNT(*)',
                'successful_deliveries' => 'SUM(success)'
            ])
            ->where('created_at >= ?', $startDate)
            ->where('created_at <= ?', $endDate . ' 23:59:59');

        $row = $connection->fetchRow($select) ?: [];
        return [
            'total_queries' => (int)($row['total_queries'] ?? 0),
            'successful_deliveries' => (int)($row['successful_deliveries'] ?? 0),
            'express_deliveries' => 0
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRevenueByDeliveryType($startDate, $endDate)
    {
        $connection = $this->getConnection();
        $table = $this->resourceConnection->getTableName('domus_delivery_revenue');

        if (!$connection->isTableExists($table)) {
            return [
                'standard' => 0.00,
                'express' => 0.00
            ];
        }

        $select = $connection->select()
            ->from($table, ['delivery_type', 'revenue' => 'SUM(revenue)'])
            ->where('created_at >= ?', $startDate)
            ->where('created_at <= ?', $endDate . ' 23:59:59')
            ->group('delivery_type');

        $rows = $connection->fetchAll($select);
        $data = ['standard' => 0.00, 'express' => 0.00];
        foreach ($rows as $row) {
            $type = (string)($row['delivery_type'] ?? '');
            if (array_key_exists($type, $data)) {
                $data[$type] = (float)$row['revenue'];
            }
        }

        return $data;
    }

    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection();
    }
}

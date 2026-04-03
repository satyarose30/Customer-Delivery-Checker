<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Analytics;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Class DeliveryMetrics
 * @package Domus\CustomerDeliveryChecker\Model\Analytics
 */
class DeliveryMetrics
{
    private const ANALYTICS_TABLE = 'domus_delivery_analytics';
    private const EXPRESS_ANALYTICS_TABLE = 'domus_express_delivery_analytics';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;
    
    /**
     * @var DateTime
     */
    private DateTime $dateTime;
    
    /**
     * @var OrderCollectionFactory
     */
    private OrderCollectionFactory $orderCollectionFactory;

    /**
     * DeliveryMetrics constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param DateTime $dateTime
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        DateTime $dateTime,
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->dateTime = $dateTime;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Get comprehensive delivery metrics
     *
     * @param array $filters
     * @return array
     */
    public function getDeliveryMetrics(array $filters = []): array
    {
        return [
            'total_checks' => $this->getTotalChecks($filters),
            'success_rate' => $this->getSuccessRate($filters),
            'top_pincodes' => $this->getTopPincodes($filters),
            'delivery_types' => $this->getDeliveryTypeDistribution($filters),
            'failed_reasons' => $this->getFailedReasons($filters),
            'peak_times' => $this->getPeakCheckTimes($filters),
            'revenue_impact' => $this->getRevenueImpact($filters),
            'express_adoption' => $this->getExpressAdoptionRate($filters),
            'regional_performance' => $this->getRegionalPerformance($filters)
        ];
    }

    /**
     * Get total pincode checks
     *
     * @param array $filters
     * @return int
     */
    public function getTotalChecks(array $filters = []): int
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::ANALYTICS_TABLE);
        if (!$this->tableExists($tableName)) {
            return 0;
        }
        
        $select = $connection->select()
            ->from($tableName, ['COUNT(*) as total'])
            ->where('created_at >= ?', $this->getDateFromFilter($filters));
            
        return (int) $connection->fetchOne($select);
    }

    /**
     * Get success rate
     *
     * @param array $filters
     * @return float
     */
    public function getSuccessRate(array $filters = []): float
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::ANALYTICS_TABLE);
        if (!$this->tableExists($tableName)) {
            return 0.0;
        }
        
        $select = $connection->select()
            ->from($tableName, [
                'total' => 'COUNT(*)',
                'successful' => 'SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END)'
            ])
            ->where('created_at >= ?', $this->getDateFromFilter($filters));
            
        $result = $connection->fetchRow($select);
        
        return $result['total'] > 0 ? ($result['successful'] / $result['total']) * 100 : 0;
    }

    /**
     * Get top serviceable pincodes
     *
     * @param array $filters
     * @param int $limit
     * @return array
     */
    public function getTopPincodes(array $filters = [], int $limit = 10): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::ANALYTICS_TABLE);
        if (!$this->tableExists($tableName)) {
            return [];
        }
        
        $select = $connection->select()
            ->from($tableName, [
                'pincode',
                'checks' => 'COUNT(*)',
                'success_rate' => '(SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100'
            ])
            ->where('created_at >= ?', $this->getDateFromFilter($filters))
            ->group('pincode')
            ->order('checks DESC')
            ->limit($limit);
            
        return $connection->fetchAll($select);
    }

    /**
     * Get delivery type distribution
     *
     * @param array $filters
     * @return array
     */
    public function getDeliveryTypeDistribution(array $filters = []): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::EXPRESS_ANALYTICS_TABLE);
        if (!$this->tableExists($tableName)) {
            return [];
        }
        
        $select = $connection->select()
            ->from($tableName, [
                'delivery_type',
                'count' => 'COUNT(*)',
                'percentage' => 'ROUND((COUNT(*) / (SELECT COUNT(*) FROM ' . $tableName . ')) * 100, 2)'
            ])
            ->where('created_at >= ?', $this->getDateFromFilter($filters))
            ->group('delivery_type');
            
        return $connection->fetchAll($select);
    }

    /**
     * Get failed delivery reasons
     *
     * @param array $filters
     * @return array
     */
    public function getFailedReasons(array $filters = []): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::ANALYTICS_TABLE);
        if (!$this->tableExists($tableName)) {
            return [];
        }
        
        $select = $connection->select()
            ->from($tableName, [
                'failure_reason',
                'count' => 'COUNT(*)'
            ])
            ->where('success = 0')
            ->where('created_at >= ?', $this->getDateFromFilter($filters))
            ->where('failure_reason IS NOT NULL')
            ->group('failure_reason')
            ->order('count DESC');
            
        return $connection->fetchAll($select);
    }

    /**
     * Get peak check times
     *
     * @param array $filters
     * @return array
     */
    public function getPeakCheckTimes(array $filters = []): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::ANALYTICS_TABLE);
        if (!$this->tableExists($tableName)) {
            return [];
        }
        
        $select = $connection->select()
            ->from($tableName, [
                'hour' => 'HOUR(created_at)',
                'count' => 'COUNT(*)'
            ])
            ->where('created_at >= ?', $this->getDateFromFilter($filters))
            ->group('HOUR(created_at)')
            ->order('hour');
            
        return $connection->fetchAll($select);
    }

    /**
     * Get revenue impact of delivery options
     *
     * @param array $filters
     * @return array
     */
    public function getRevenueImpact(array $filters = []): array
    {
        $orderCollection = $this->orderCollectionFactory->create();
        
        $orderCollection->addAttributeToFilter('created_at', ['gteq' => $this->getDateFromFilter($filters)])
                       ->addAttributeToFilter('delivery_pincode', ['notnull' => true]);
        
        $revenueData = [
            'total_revenue' => 0,
            'express_revenue' => 0,
            'order_count' => 0,
            'express_order_count' => 0,
            'aov' => 0
        ];
        
        foreach ($orderCollection as $order) {
            $revenueData['total_revenue'] += $order->getGrandTotal();
            $revenueData['order_count']++;
            
            if ($order->getDeliveryType() === 'express') {
                $revenueData['express_revenue'] += $order->getGrandTotal();
                $revenueData['express_order_count']++;
            }
        }
        
        if ($revenueData['order_count'] > 0) {
            $revenueData['aov'] = $revenueData['total_revenue'] / $revenueData['order_count'];
        }
        
        return $revenueData;
    }

    /**
     * Get express delivery adoption rate
     *
     * @param array $filters
     * @return float
     */
    public function getExpressAdoptionRate(array $filters = []): float
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::EXPRESS_ANALYTICS_TABLE);
        if (!$this->tableExists($tableName)) {
            return 0.0;
        }
        
        $select = $connection->select()
            ->from($tableName, [
                'total' => 'COUNT(*)',
                'express' => 'SUM(CASE WHEN delivery_type IN ("express", "overnight") THEN 1 ELSE 0 END)'
            ])
            ->where('created_at >= ?', $this->getDateFromFilter($filters));
            
        $result = $connection->fetchRow($select);
        
        return $result['total'] > 0 ? ($result['express'] / $result['total']) * 100 : 0;
    }

    /**
     * Get regional performance metrics
     *
     * @param array $filters
     * @return array
     */
    public function getRegionalPerformance(array $filters = []): array
    {
        $connection = $this->resourceConnection->getConnection();
        $pincodeTable = $this->resourceConnection->getTableName('domus_delivery_pincode');
        $analyticsTable = $this->resourceConnection->getTableName(self::ANALYTICS_TABLE);
        if (!$this->tableExists($pincodeTable) || !$this->tableExists($analyticsTable)) {
            return [];
        }
        
        $select = $connection->select()
            ->from(['p' => $pincodeTable], ['state'])
            ->joinLeft(['a' => $analyticsTable], 'p.pincode = a.pincode', [
                'checks' => 'COUNT(a.pincode)',
                'success_rate' => 'ROUND((SUM(CASE WHEN a.success = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(a.pincode), 0)) * 100, 2)'
            ])
            ->where('a.created_at >= ?', $this->getDateFromFilter($filters))
            ->where('p.pincode IS NOT NULL')
            ->group('p.state')
            ->order('checks DESC');
            
        return $connection->fetchAll($select);
    }

    /**
     * Get date from filter
     *
     * @param array $filters
     * @return string
     */
    private function getDateFromFilter(array $filters): string
    {
        $dateFilter = $filters['date_from'] ?? '30 days ago';
        
        return $this->dateTime->gmtDate('Y-m-d H:i:s', strtotime($dateFilter));
    }

    private function tableExists(string $tableName): bool
    {
        return (bool)$this->resourceConnection->getConnection()->isTableExists($tableName);
    }
}

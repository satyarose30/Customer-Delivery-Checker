<?php
namespace Domus\CustomerDeliveryChecker\Model\Analytics;

class DeliveryRevenue
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Log revenue allocated for a specific delivery method
     *
     * @param string $deliveryType
     * @param float $revenue
     * @param int $orderId
     */
    public function logRevenue($deliveryType, $revenue, $orderId)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('domus_delivery_revenue');
            
            $connection->insert($tableName, [
                'delivery_type' => $deliveryType,
                'revenue' => $revenue,
                'order_id' => $orderId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            // Silently drop metrics on DB fail to prevent checkout crashing
        }
    }
}

<?php
namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\ExpressManagementInterface;
use Domus\CustomerDeliveryChecker\Api\Data\ExpressResultInterfaceFactory;

class ExpressManagement implements ExpressManagementInterface
{
    /**
     * @var ExpressResultInterfaceFactory
     */
    private $expressResultFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ExpressResultInterfaceFactory $expressResultFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->expressResultFactory = $expressResultFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritdoc
     */
    public function checkExpressDelivery($pincode, $sku = null)
    {
        $result = $this->expressResultFactory->create();
        $result->setIsEligible(false);
        $result->setAdditionalCost(0.00);

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('domus_pincode_checker');

        // Check express eligibility on generic abstract checker table, assuming columns exist
        if ($connection->isTableExists($tableName)) {
            $query = $connection->select()
                ->from($tableName, ['is_express_delivery_available', 'express_delivery_charge'])
                ->where('pincode = ?', $pincode);

            $expressData = $connection->fetchRow($query);

            if ($expressData && isset($expressData['is_express_delivery_available']) && $expressData['is_express_delivery_available']) {
                $result->setIsEligible(true);
                $result->setAdditionalCost(isset($expressData['express_delivery_charge']) ? (float)$expressData['express_delivery_charge'] : 50.00);
            }
        }
        
        return $result;
    }
}

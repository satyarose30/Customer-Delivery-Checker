<?php
namespace Domus\CustomerDeliveryChecker\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class AddDeliverySlots implements SchemaPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('domus_delivery_slots');

        if (!$connection->isTableExists($tableName)) {
            $table = $connection->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'pincode',
                    Table::TYPE_TEXT,
                    10,
                    ['nullable' => false],
                    'Pincode'
                )
                ->addColumn(
                    'day_of_week',
                    Table::TYPE_TEXT,
                    20,
                    ['nullable' => false],
                    'Day of Week'
                )
                ->addColumn(
                    'time_from',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Start Time'
                )
                ->addColumn(
                    'time_to',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'End Time'
                )
                ->addColumn(
                    'max_orders',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Maximum Orders'
                )
                ->addColumn(
                    'current_orders',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Current Orders'
                )
                ->addColumn(
                    'is_active',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 1],
                    'Is Active'
                )
                ->setComment('Delivery Slots Table');

            $connection->createTable($table);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}

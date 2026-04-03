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
                    'slot_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Slot ID'
                )
                ->addColumn(
                    'pincode',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Pincode'
                )
                ->addColumn(
                    'start_time',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false],
                    'Start Time'
                )
                ->addColumn(
                    'end_time',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false],
                    'End Time'
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

<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class AddDeliverySlots implements SchemaPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('domus_delivery_schedule');

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
                    'pincode_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Pincode ID'
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
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Start Time'
                )
                ->addColumn(
                    'time_to',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'End Time'
                )
                ->addColumn(
                    'is_available',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 1],
                    'Availability Flag'
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
                ->addForeignKey(
                    $this->moduleDataSetup->getFKName(
                        $tableName,
                        'pincode_id',
                        'domus_delivery_pincode',
                        'entity_id'
                    ),
                    'pincode_id',
                    $this->moduleDataSetup->getTable('domus_delivery_pincode'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Delivery Schedule Table');

            $connection->createTable($table);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}

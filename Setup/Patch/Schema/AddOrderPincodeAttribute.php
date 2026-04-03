<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class AddOrderPincodeAttribute implements SchemaPatchInterface
{
    private SchemaSetupInterface $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    public function apply()
    {
        $this->schemaSetup->startSetup();

        $connection = $this->schemaSetup->getConnection();

        if (!$connection->tableColumnExists('sales_order', 'delivery_pincode')) {
            $connection->addColumn(
                $this->schemaSetup->getTable('sales_order'),
                'delivery_pincode',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Delivery Pincode'
                ]
            );
        }

        $this->schemaSetup->endSetup();
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
<?php
namespace Domus\CustomerDeliveryChecker\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Older Magento 2 methodology, but keeping for backward compatibility if needed.
        // Data patches are predominantly used in newer Magento 2.3+ versions for schema setups.
        
        $setup->endSetup();
    }
}

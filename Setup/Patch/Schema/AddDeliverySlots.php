<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

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
        /**
         * Intentionally no-op.
         *
         * Delivery schedule table ownership now lives in declarative schema
         * (`etc/db_schema.xml`) to avoid dual ownership/drift between
         * imperative schema patches and declarative definitions.
         */

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

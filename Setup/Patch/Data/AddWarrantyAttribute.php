<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class AddWarrantyAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $eavSetup = $this->eavSetupFactory->create([
            'setup' => $this->moduleDataSetup
        ]);

        // ✅ DO NOT create warranty (already exists)

        // Add warranty_duration (only if not exists)
        if (!$eavSetup->getAttributeId(Product::ENTITY, 'warranty_duration')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'warranty_duration',
                [
                    'type' => 'varchar',
                    'label' => 'Warranty Duration',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 160,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'used_in_product_listing' => true,
                    'visible_on_front' => true,
                    'visible' => true,
                    'note' => 'e.g., 2 years, 6 months, 90 days',
                    'group' => 'Warranty Information'
                ]
            );
        }

        // Add warranty_details (only if not exists)
        if (!$eavSetup->getAttributeId(Product::ENTITY, 'warranty_details')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'warranty_details',
                [
                    'type' => 'text',
                    'label' => 'Warranty Details',
                    'input' => 'textarea',
                    'required' => false,
                    'sort_order' => 170,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'used_in_product_listing' => true,
                    'visible_on_front' => true,
                    'visible' => true,
                    'note' => 'Detailed warranty info, return conditions, etc.',
                    'group' => 'Warranty Information'
                ]
            );
        }

        $this->moduleDataSetup->endSetup();
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
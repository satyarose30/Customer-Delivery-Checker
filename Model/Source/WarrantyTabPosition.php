<?php
// app/code/Domus/CustomerDeliveryChecker/Model/Source/WarrantyTabPosition.php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class WarrantyTabPosition
 * @package Domus\CustomerDeliveryChecker\Model\Source
 */
class WarrantyTabPosition implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'before', 'label' => __('Before Pincode Check')],
            ['value' => 'after', 'label' => __('After Pincode Check')],
            ['value' => 'with_results', 'label' => __('With Results')],
            ['value' => 'separate', 'label' => __('Separate Section')]
        ];
    }
}

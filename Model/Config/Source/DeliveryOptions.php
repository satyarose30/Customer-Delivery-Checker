<?php
namespace Domus\CustomerDeliveryChecker\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DeliveryOptions implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'standard', 'label' => __('Standard Delivery')],
            ['value' => 'express', 'label' => __('Express Delivery')],
            ['value' => 'same_day', 'label' => __('Same Day Delivery')],
            ['value' => 'scheduled', 'label' => __('Scheduled Delivery')]
        ];
    }
}

<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class DeliverySlots
 * @package Domus\CustomerDeliveryChecker\Model\Source
 */
class DeliverySlots implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'any_time', 'label' => __('Any Time (9 AM - 9 PM)')],
            ['value' => 'morning', 'label' => __('Morning (9 AM - 12 PM)')],
            ['value' => 'afternoon', 'label' => __('Afternoon (12 PM - 5 PM)')],
            ['value' => 'evening', 'label' => __('Evening (5 PM - 9 PM)')],
            ['value' => 'night', 'label' => __('Night (9 PM - 12 AM)')],
            ['value' => 'early_morning', 'label' => __('Early Morning (6 AM - 9 AM)')]
        ];
    }

    /**
     * Get slot time ranges
     *
     * @return array
     */
    public function getSlotTimeRanges(): array
    {
        return [
            'early_morning' => ['06:00', '09:00'],
            'morning' => ['09:00', '12:00'],
            'afternoon' => ['12:00', '17:00'],
            'evening' => ['17:00', '21:00'],
            'night' => ['21:00', '00:00'],
            'any_time' => ['09:00', '21:00']
        ];
    }
}

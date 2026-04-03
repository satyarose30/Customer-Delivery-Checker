<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LogisticsProvider implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'manual', 'label' => __('Manual/Internal Rules Only')],
            ['value' => 'shiprocket', 'label' => __('Shiprocket Integration')],
            ['value' => 'third_party', 'label' => __('Third-party Logistics')]
        ];
    }
}

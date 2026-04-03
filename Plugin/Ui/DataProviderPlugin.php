<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Plugin\Ui;

use Magento\Ui\Component\Form;
use Domus\CustomerDeliveryChecker\Helper\Data;

class DataProviderPlugin
{
    protected Data $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function afterGetData($subject, $result)
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        foreach ($result as &$item) {
            if (isset($item['pincode'])) {
                if (!isset($item['delivery_options'])) {
                    $item['delivery_options'] = $this->getDefaultDeliveryOptions();
                }
                
                if (!isset($item['is_deliverable'])) {
                    $item['show_delivery_fields'] = true;
                }
            }
        }

        return $result;
    }

    public function afterGetMeta($subject, array $meta)
    {
        if (strpos($subject->getPrimaryFieldName(), 'pincode') !== false) {
            $meta['delivery_options']['arguments']['data']['config']['options'] = [
                ['value' => 'standard', 'label' => 'Standard Delivery'],
                ['value' => 'express', 'label' => 'Express Delivery'],
                ['value' => 'overnight', 'label' => 'Overnight Delivery'],
            ];
        }
        return $meta;
    }

    private function getDefaultDeliveryOptions(): array
    {
        return [
            'standard' => [
                'label' => 'Standard Delivery',
                'default' => true
            ],
            'express' => [
                'label' => 'Express Delivery',
                'default' => false
            ],
            'overnight' => [
                'label' => 'Overnight Delivery',
                'default' => false
            ]
        ];
    }
}
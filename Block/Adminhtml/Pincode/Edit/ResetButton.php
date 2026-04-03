<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Pincode\Edit;

use Magento\Backend\Block\Widget\Context;

class ResetButton extends GenericButton
{
    public function __construct(Context $context)
    {
        parent::__construct($context, $context->getUrlBuilder());
    }

    public function getButtonData(): array
    {
        return [
            'label' => __('Reset'),
            'on_click' => 'location.reload();',
            'class' => 'reset',
            'sort_order' => 20
        ];
    }
}

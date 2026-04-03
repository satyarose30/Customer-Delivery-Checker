<?php
namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Pincode;

use Magento\Backend\Block\Template;

class Export extends Template
{
    protected $_template = 'pincode/export.phtml';

    public function getExportUrl()
    {
        return $this->getUrl('*/*/exportPost');
    }
}

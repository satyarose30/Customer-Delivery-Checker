<?php
namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Pincode;

use Magento\Backend\Block\Template;

class Import extends Template
{
    protected $_template = 'pincode/import.phtml';

    public function getImportUrl()
    {
        return $this->getUrl('*/*/importPost');
    }
}

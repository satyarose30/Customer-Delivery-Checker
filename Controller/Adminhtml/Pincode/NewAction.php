<?php
namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Pincode;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends Action
{
    const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::pincode_save';

    public function execute()
    {
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('edit');
    }
}

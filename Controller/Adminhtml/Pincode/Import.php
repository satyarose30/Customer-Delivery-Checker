<?php
namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Pincode;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Import extends Action
{
    const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::pincode_import';

    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Import Delivery Pincodes'));

        return $resultPage;
    }
}

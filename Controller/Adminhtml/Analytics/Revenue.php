<?php
namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Analytics;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Revenue extends Action
{
    const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::analytics';

    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Delivery Revenue Analytics'));

        return $resultPage;
    }
}

<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Schedule;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::manage';

    public function execute(): Redirect
    {
        $this->messageManager->addNoticeMessage(
            __('Delivery schedule management will be available in a dedicated grid soon. Redirected to pincode rules.')
        );

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('customerdeliverychecker/pincode/index');
    }
}


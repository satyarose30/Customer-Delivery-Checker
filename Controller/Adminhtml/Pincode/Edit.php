<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Pincode;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Domus\CustomerDeliveryChecker\Model\PincodeFactory;
use Magento\Framework\Registry;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::manage';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly PincodeFactory $pincodeFactory,
        private readonly Registry $registry
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->pincodeFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This pincode no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->registry->register('domus_pincode', $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Domus_CustomerDeliveryChecker::manage');
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ? __('Edit Pincode: %1', $model->getPincode()) : __('New Pincode')
        );

        return $resultPage;
    }
}

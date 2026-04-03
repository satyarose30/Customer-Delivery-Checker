<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Pincode;

use Domus\CustomerDeliveryChecker\Model\PincodeRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::pincode_delete';

    public function __construct(
        Context $context,
        private readonly PincodeRepository $pincodeRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $entityId = (int)$this->getRequest()->getParam('entity_id');

        if ($entityId) {
            try {
                $pincode = $this->pincodeRepository->getById($entityId);
                $this->pincodeRepository->delete($pincode);
                $this->messageManager->addSuccessMessage(__('The pincode has been deleted.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('An error occurred while deleting the pincode.')
                );
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}

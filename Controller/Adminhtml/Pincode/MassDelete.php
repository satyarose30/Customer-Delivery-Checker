<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Pincode;

use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    public const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::pincode_delete';

    public function __construct(
        Context $context,
        private readonly Filter $filter,
        private readonly CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $deletedCount = 0;

        foreach ($collection as $item) {
            $item->delete();
            $deletedCount++;
        }

        if ($deletedCount) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $deletedCount)
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}

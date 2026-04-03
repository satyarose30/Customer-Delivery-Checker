<?php
namespace Domus\CustomerDeliveryChecker\Controller\Rest;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Domus\CustomerDeliveryChecker\Api\SearchManagementInterface;

class Autocomplete extends Action
{
    protected $jsonFactory;
    protected $searchManagement;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        SearchManagementInterface $searchManagement
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->searchManagement = $searchManagement;
        parent::__construct($context);
    }

    public function execute()
    {
        $query = $this->getRequest()->getParam('q');

        $result = $this->jsonFactory->create();
        try {
            $searchResults = $this->searchManagement->searchPincodes($query);
            return $result->setData([
                'success' => true,
                'items' => $searchResults->getItems()
            ]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

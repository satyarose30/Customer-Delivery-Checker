<?php
namespace Domus\CustomerDeliveryChecker\Controller\Rest;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Domus\CustomerDeliveryChecker\Api\SearchManagementInterface;
use Psr\Log\LoggerInterface;

class Autocomplete extends Action
{
    protected $jsonFactory;
    protected $searchManagement;
    protected LoggerInterface $logger;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        SearchManagementInterface $searchManagement,
        LoggerInterface $logger
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->searchManagement = $searchManagement;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $query = (string)$this->getRequest()->getParam('q', '');

        $result = $this->jsonFactory->create();
        try {
            $searchResults = $this->searchManagement->autocomplete($query);
            return $result->setData([
                'success' => true,
                'items' => $searchResults->getItems()
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Autocomplete API failed', ['exception' => $e]);
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

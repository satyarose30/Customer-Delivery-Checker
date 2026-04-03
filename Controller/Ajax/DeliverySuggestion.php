<?php
namespace Domus\CustomerDeliveryChecker\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class DeliverySuggestion extends Action
{
    protected $jsonFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        return $result->setData([
            'success' => true,
            'suggestion' => __('Consider upgrading to express delivery for faster shipping!')
        ]);
    }
}

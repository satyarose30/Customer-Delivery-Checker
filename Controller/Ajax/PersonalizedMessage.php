<?php
namespace Domus\CustomerDeliveryChecker\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Domus\CustomerDeliveryChecker\Model\Delivery\PersonalizedService;

class PersonalizedMessage extends Action
{
    protected $jsonFactory;
    protected $personalizedService;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        PersonalizedService $personalizedService
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->personalizedService = $personalizedService;
        parent::__construct($context);
    }

    public function execute()
    {
        $pincode = $this->getRequest()->getParam('pincode');
        $customerId = $this->getRequest()->getParam('customer_id');
        
        $result = $this->jsonFactory->create();
        return $result->setData([
            'success' => true,
            'message' => $this->personalizedService->getPersonalizedMessage($customerId, $pincode)
        ]);
    }
}

<?php
namespace Domus\CustomerDeliveryChecker\Controller\Rest;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Domus\CustomerDeliveryChecker\Api\TrackingManagementInterface;

class Track extends Action
{
    protected $jsonFactory;
    protected $trackingManagement;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        TrackingManagementInterface $trackingManagement
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->trackingManagement = $trackingManagement;
        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        $result = $this->jsonFactory->create();
        try {
            $trackingResult = $this->trackingManagement->trackOrder($orderId);
            return $result->setData([
                'success' => true,
                'status' => $trackingResult->getStatus(),
                'details' => $trackingResult->getDetails()
            ]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

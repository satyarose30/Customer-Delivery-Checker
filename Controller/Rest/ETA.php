<?php
namespace Domus\CustomerDeliveryChecker\Controller\Rest;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Domus\CustomerDeliveryChecker\Api\ETAManagementInterface;

class ETA extends Action
{
    protected $jsonFactory;
    protected $etaManagement;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ETAManagementInterface $etaManagement
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->etaManagement = $etaManagement;
        parent::__construct($context);
    }

    public function execute()
    {
        $pincode = $this->getRequest()->getParam('pincode');
        $sku = $this->getRequest()->getParam('sku');

        $result = $this->jsonFactory->create();
        try {
            $etaResult = $this->etaManagement->calculateETA($pincode, $sku);
            return $result->setData([
                'success' => true,
                'eta' => $etaResult->getEstimatedDeliveryDate(),
                'days' => $etaResult->getDeliveryDays()
            ]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

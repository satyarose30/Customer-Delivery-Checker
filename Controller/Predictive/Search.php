<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Predictive;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Magento\Framework\App\RequestInterface;

class Search implements HttpGetActionInterface
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly JsonFactory $resultJsonFactory,
        private readonly RequestInterface $request
    ) {}

    public function execute()
    {
        $searchTerm = trim($this->request->getParam('q', ''));
        $countryId = $this->request->getParam('country_id', 'IN');
        $limit = (int)$this->request->getParam('limit', 10);

        $result = $this->resultJsonFactory->create();

        if (strlen($searchTerm) < 2) {
            return $result->setData([]);
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('pincode', ['like' => $searchTerm . '%']);
        $collection->addFieldToFilter('country_id', $countryId);
        $collection->addFieldToFilter('is_active', 1);
        $collection->addFieldToFilter('is_deliverable', 1);
        $collection->setPageSize($limit);

        $pincodes = [];
        foreach ($collection as $item) {
            $pincodes[] = [
                'pincode' => $item->getPincode(),
                'city' => $item->getCity(),
                'state' => $item->getState(),
                'area' => $item->getAreaName(),
                'cod_available' => $item->getIsCodAvailable(),
                'delivery_days' => $item->getEstimatedDeliveryDays()
            ];
        }

        return $result->setData([
            'success' => true,
            'data' => $pincodes
        ]);
    }
}

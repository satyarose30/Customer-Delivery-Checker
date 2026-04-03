<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\SearchManagementInterface;
use Domus\CustomerDeliveryChecker\Api\Data\SearchResultInterfaceFactory;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory as PincodeCollectionFactory;

class SearchManagement implements SearchManagementInterface
{
    public function __construct(
        private readonly SearchResultInterfaceFactory $searchResultFactory,
        private readonly PincodeCollectionFactory $pincodeCollectionFactory
    ) {
    }

    /**
     * @inheritdoc
     */
    public function searchPincodes($query)
    {
        $result = $this->searchResultFactory->create();
        $query = trim((string)$query);
        if ($query === '') {
            $result->setItems([]);
            $result->setTotalCount(0);
            return $result;
        }

        $collection = $this->pincodeCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->addFieldToFilter(
            ['pincode', 'city', 'state'],
            [
                ['like' => $query . '%'],
                ['like' => '%' . $query . '%'],
                ['like' => '%' . $query . '%']
            ]
        );
        $collection->setPageSize(10)->setCurPage(1);

        $result->setItems($collection->getItems());
        $result->setTotalCount((int)$collection->getSize());
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function autocomplete($query)
    {
        return $this->searchPincodes($query);
    }
}

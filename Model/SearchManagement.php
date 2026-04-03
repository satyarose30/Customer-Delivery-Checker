<?php
namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\SearchManagementInterface;
use Domus\CustomerDeliveryChecker\Api\Data\SearchResultInterfaceFactory;

class SearchManagement implements SearchManagementInterface
{
    /**
     * @var SearchResultInterfaceFactory
     */
    private $searchResultFactory;

    public function __construct(
        SearchResultInterfaceFactory $searchResultFactory
    ) {
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritdoc
     */
    public function searchPincodes($query)
    {
        $result = $this->searchResultFactory->create();
        $result->setItems([]);
        $result->setTotalCount(0);

        // TODO: Elasticsearch / OpenSearch query for pincodes
        
        return $result;
    }
}

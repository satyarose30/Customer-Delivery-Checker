<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\Data\PincodeInterface;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode as PincodeResource;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PincodeRepository
{
    public function __construct(
        private readonly PincodeResource $resource,
        private readonly PincodeFactory $pincodeFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    public function getById(int $entityId): PincodeInterface
    {
        $pincode = $this->pincodeFactory->create();
        $this->resource->load($pincode, $entityId);
        if (!$pincode->getEntityId()) {
            throw new NoSuchEntityException(__('Pincode with id "%1" does not exist.', $entityId));
        }
        return $pincode;
    }

    public function getByPincode(string $pincode, string $countryId = 'IN'): ?PincodeInterface
    {
        $model = $this->pincodeFactory->create();
        $this->resource->load($model, $pincode, 'pincode');
        if ($model->getEntityId()
            && $model->getCountryId() === $countryId
            && $model->getIsActive()
        ) {
            return $model;
        }
        return null;
    }

    public function save(PincodeInterface $pincode): PincodeInterface
    {
        try {
            $this->resource->save($pincode);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save pincode: %1', $e->getMessage()), $e);
        }
        return $pincode;
    }

    public function delete(PincodeInterface $pincode): bool
    {
        try {
            $this->resource->delete($pincode);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete pincode: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}

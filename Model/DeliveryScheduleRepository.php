<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleInterface;
use Domus\CustomerDeliveryChecker\Api\Data\DeliveryScheduleSearchResultsInterface;
use Domus\CustomerDeliveryChecker\Api\DeliveryScheduleRepositoryInterface;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\DeliverySchedule\CollectionFactory;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\DeliverySchedule as DeliveryScheduleResource;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class DeliveryScheduleRepository implements DeliveryScheduleRepositoryInterface
{
    public function __construct(
        private readonly DeliveryScheduleResource $resource,
        private readonly DeliveryScheduleFactory $factory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    public function getById(int $id): DeliveryScheduleInterface
    {
        $schedule = $this->factory->create();
        $this->resource->load($schedule, $id);
        if (!$schedule->getEntityId()) {
            throw new NoSuchEntityException(__('Schedule with id "%1" does not exist.', $id));
        }
        return $schedule;
    }

    public function save(DeliveryScheduleInterface $schedule): DeliveryScheduleInterface
    {
        try {
            $this->resource->save($schedule);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save schedule: %1', $e->getMessage()), $e);
        }
        return $schedule;
    }

    public function delete(DeliveryScheduleInterface $schedule): bool
    {
        try {
            $this->resource->delete($schedule);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete schedule: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function getList(SearchCriteriaInterface $criteria): DeliveryScheduleSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    public function getByPincodeId(int $pincodeId): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('pincode_id', $pincodeId);
        $collection->setOrder('day_of_week', 'ASC');
        return $collection->getItems();
    }

    public function getAvailableSlots(int $pincodeId): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('pincode_id', $pincodeId);
        $collection->addFieldToFilter('is_available', 1);

        $availableSlots = [];
        foreach ($collection as $slot) {
            $maxOrders = $slot->getMaxOrders();
            if ($maxOrders === null || $slot->getCurrentOrders() < $maxOrders) {
                $availableSlots[] = $slot;
            }
        }

        return $availableSlots;
    }
}
<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\ResourceModel\DeliverySchedule;

use Domus\CustomerDeliveryChecker\Model\DeliverySchedule as ScheduleModel;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\DeliverySchedule as ScheduleResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(ScheduleModel::class, ScheduleResource::class);
    }
}
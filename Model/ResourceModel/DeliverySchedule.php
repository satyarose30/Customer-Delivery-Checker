<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DeliverySchedule extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('domus_delivery_schedule', 'entity_id');
    }
}
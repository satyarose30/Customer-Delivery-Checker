<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\TimeSlotManagementInterface;
use Domus\CustomerDeliveryChecker\Api\Data\TimeSlotResultInterfaceFactory;

class TimeSlotManagement implements TimeSlotManagementInterface
{
    private TimeSlotResultInterfaceFactory $timeSlotResultFactory;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    public function __construct(
        TimeSlotResultInterfaceFactory $timeSlotResultFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->timeSlotResultFactory = $timeSlotResultFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritdoc
     */
    public function getAvailableSlots($pincode)
    {
        $result = $this->timeSlotResultFactory->create();
        $result->setIsAvailable(false);
        $result->setAvailableSlots([]);
        
        $connection = $this->resourceConnection->getConnection();
        $scheduleTable = $this->resourceConnection->getTableName('domus_delivery_schedule');
        $pincodeTable = $this->resourceConnection->getTableName('domus_delivery_pincode');
        if (!$connection->isTableExists($scheduleTable) || !$connection->isTableExists($pincodeTable)) {
            return $result;
        }

        $query = $connection->select()
            ->from(['s' => $scheduleTable], ['day_of_week', 'time_from', 'time_to', 'max_orders', 'current_orders'])
            ->joinInner(['p' => $pincodeTable], 's.pincode_id = p.entity_id', [])
            ->where('p.pincode = ?', $pincode)
            ->where('s.is_available = ?', 1);

        $slotsData = $connection->fetchAll($query);

        if (!empty($slotsData)) {
            $result->setIsAvailable(true);
            $result->setAvailableSlots($slotsData);
        }
        
        return $result;
    }
}

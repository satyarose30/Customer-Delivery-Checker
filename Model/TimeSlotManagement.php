<?php
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
        $tableName = $this->resourceConnection->getTableName('domus_delivery_slots');

        $query = $connection->select()
            ->from($tableName, ['day_of_week', 'time_from', 'time_to', 'max_orders', 'current_orders'])
            ->where('pincode = ?', $pincode)
            ->where('is_active = ?', 1);

        $slotsData = $connection->fetchAll($query);

        if (!empty($slotsData)) {
            $result->setIsAvailable(true);
            $result->setAvailableSlots($slotsData);
        }
        
        return $result;
    }
}

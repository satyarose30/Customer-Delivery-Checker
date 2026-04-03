<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Magento\Framework\ObjectManagerInterface;

class DeliveryScheduleFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data = []): DeliverySchedule
    {
        return $this->objectManager->create(DeliverySchedule::class, $data);
    }
}
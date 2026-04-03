<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Magento\Framework\ObjectManagerInterface;

class PincodeCheckResultFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data = []): PincodeCheckResult
    {
        return $this->objectManager->create(PincodeCheckResult::class, $data);
    }
}

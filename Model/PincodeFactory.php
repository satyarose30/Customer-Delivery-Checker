<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Magento\Framework\ObjectManagerInterface;

class PincodeFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data = []): Pincode
    {
        return $this->objectManager->create(Pincode::class, $data);
    }
}

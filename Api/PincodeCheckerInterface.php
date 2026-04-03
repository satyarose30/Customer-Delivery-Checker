<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api;

interface PincodeCheckerInterface
{
    /**
     * Check delivery availability for a pincode
     *
     * @param string $pincode
     * @param string $countryId
     * @param int|null $productId
     * @param int|null $categoryId
     * @param float|null $cartWeight
     * @param float|null $cartValue
     * @param int|null $storeId
     * @return \Domus\CustomerDeliveryChecker\Api\Data\PincodeCheckResultInterface
     */
    public function checkAdvanced(
        string $pincode,
        string $countryId = 'IN',
        ?int $productId = null,
        ?int $categoryId = null,
        ?float $cartWeight = null,
        ?float $cartValue = null,
        ?int $storeId = null
    ): Data\PincodeCheckResultInterface;

    /**
     * Check delivery availability for a pincode (legacy, simple version)
     *
     * @param string $pincode
     * @param string $countryId
     * @return \Domus\CustomerDeliveryChecker\Api\Data\PincodeCheckResultInterface
     */
    public function check(string $pincode, string $countryId = 'IN'): Data\PincodeCheckResultInterface;
}
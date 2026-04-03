<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api;

use Domus\CustomerDeliveryChecker\Api\Data\PincodeCheckResultInterface;

interface PincodeManagementInterface
{
    /**
     * Check pincode delivery availability
     *
     * @param string $pincode
     * @param string $countryId
     * @param int|null $productId
     * @param int|null $categoryId
     * @param float|null $cartWeight
     * @param float|null $cartValue
     * @return \Domus\CustomerDeliveryChecker\Api\Data\PincodeCheckResultInterface
     */
    public function checkPincode(
        string $pincode,
        string $countryId = 'IN',
        ?int $productId = null,
        ?int $categoryId = null,
        ?float $cartWeight = null,
        ?float $cartValue = null
    ): PincodeCheckResultInterface;

    /**
     * Check multiple pincodes at once
     *
     * @param string[] $pincodes
     * @param string $countryId
     * @return \Domus\CustomerDeliveryChecker\Api\Data\PincodeCheckResultInterface[]
     */
    public function checkMultiplePincodes(array $pincodes, string $countryId = 'IN'): array;

    /**
     * Get available delivery slots for pincode
     *
     * @param string $pincode
     * @param string $countryId
     * @return array
     */
    public function getAvailableDeliverySlots(string $pincode, string $countryId = 'IN'): array;

    /**
     * Validate pincode for express delivery
     *
     * @param string $pincode
     * @param string $countryId
     * @return bool
     */
    public function isExpressDeliveryAvailable(string $pincode, string $countryId = 'IN'): bool;

    /**
     * Get estimated delivery date
     *
     * @param string $pincode
     * @param string $countryId
     * @param string|null $deliveryType
     * @return string
     */
    public function getEstimatedDeliveryDate(
        string $pincode,
        string $countryId = 'IN',
        ?string $deliveryType = null
    ): string;

    /**
     * Search pincodes by city or area
     *
     * @param string $query
     * @param string $countryId
     * @param int $limit
     * @return array
     */
    public function searchPincodes(string $query, string $countryId = 'IN', int $limit = 10): array;

    /**
     * Get pincode details
     *
     * @param string $pincode
     * @param string $countryId
     * @return array|null
     */
    public function getPincodeDetails(string $pincode, string $countryId = 'IN'): ?array;

    /**
     * Check if COD is available for pincode
     *
     * @param string $pincode
     * @param string $countryId
     * @return bool
     */
    public function isCodAvailable(string $pincode, string $countryId = 'IN'): bool;

    /**
     * Get shipping charges for pincode
     *
     * @param string $pincode
     * @param string $countryId
     * @param float $orderValue
     * @param float $weight
     * @return array
     */
    public function getShippingCharges(
        string $pincode,
        string $countryId = 'IN',
        float $orderValue = 0,
        float $weight = 0
    ): array;
}

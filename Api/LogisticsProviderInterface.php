<?php
namespace Domus\CustomerDeliveryChecker\Api;

interface LogisticsProviderInterface
{
    /**
     * Get delivery estimate from the provider
     *
     * @param string $pincode
     * @param float $weight
     * @return array
     */
    public function getDeliveryEstimate(string $pincode, float $weight): array;

    /**
     * Check if the pincode is available via this provider
     *
     * @param string $pincode
     * @return bool
     */
    public function isAvailable(string $pincode): bool;

    /**
     * Return the unique identifier for this provider
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Return the human-readable name of the provider
     *
     * @return string
     */
    public function getName(): string;
}

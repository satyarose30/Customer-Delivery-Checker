<?php
namespace Domus\CustomerDeliveryChecker\Api;

interface ETAManagementInterface
{
    /**
     * Calculate ETA for a product shipped to a specific pincode.
     *
     * @param string $pincode
     * @param string $sku
     * @return \Domus\CustomerDeliveryChecker\Api\Data\ETAResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function calculateETA($pincode, $sku);
}

<?php
namespace Domus\CustomerDeliveryChecker\Api;

interface ExpressManagementInterface
{
    /**
     * Check if express delivery is available for a pincode.
     *
     * @param string $pincode
     * @param string $sku
     * @return \Domus\CustomerDeliveryChecker\Api\Data\ExpressResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkExpressDelivery($pincode, $sku = null);
}

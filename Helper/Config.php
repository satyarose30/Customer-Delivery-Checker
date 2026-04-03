<?php
namespace Domus\CustomerDeliveryChecker\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    /**
     * @return string
     */
    public function getShiprocketApiKey()
    {
        return $this->scopeConfig->getValue('deliverychecker/integration/shiprocket_api_key');
    }
}

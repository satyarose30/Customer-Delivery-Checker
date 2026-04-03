<?php
namespace Domus\CustomerDeliveryChecker\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Validation extends AbstractHelper
{
    /**
     * @param string $pincode
     * @return bool
     */
    public function isValidPincode($pincode)
    {
        return preg_match('/^[1-9][0-9]{5}$/', $pincode) === 1;
    }
}

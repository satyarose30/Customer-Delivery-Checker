<?php
namespace Domus\CustomerDeliveryChecker\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class PincodeCheckObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $pincode = $observer->getEvent()->getPincode();
        // Custom logic on pincode check
    }
}

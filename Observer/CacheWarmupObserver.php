<?php
namespace Domus\CustomerDeliveryChecker\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class CacheWarmupObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        // Preheat cache on entity save
    }
}

<?php
namespace Domus\CustomerDeliveryChecker\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class IndexerObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        // Intercept reindex logic to update pincode cache
    }
}

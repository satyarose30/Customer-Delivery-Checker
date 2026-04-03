<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Dashboard;

use Magento\Backend\Block\Template;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory as PincodeCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Statistics extends Template
{
    public function __construct(
        Template\Context $context,
        private readonly PincodeCollectionFactory $pincodeCollectionFactory,
        private readonly OrderCollectionFactory $orderCollectionFactory,
        private readonly DateTime $dateTime,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getTotalPincodes(): int
    {
        $collection = $this->pincodeCollectionFactory->create();
        return (int)$collection->getSize();
    }

    public function getDeliverablePincodes(): int
    {
        $collection = $this->pincodeCollectionFactory->create();
        $collection->addFieldToFilter('is_deliverable', 1);
        $collection->addFieldToFilter('is_active', 1);
        return (int)$collection->getSize();
    }

    public function getNonDeliverablePincodes(): int
    {
        $collection = $this->pincodeCollectionFactory->create();
        $collection->addFieldToFilter('is_deliverable', 0);
        $collection->addFieldToFilter('is_active', 1);
        return (int)$collection->getSize();
    }

    public function getCodPincodes(): int
    {
        $collection = $this->pincodeCollectionFactory->create();
        $collection->addFieldToFilter('is_cod_available', 1);
        $collection->addFieldToFilter('is_active', 1);
        return (int)$collection->getSize();
    }

    public function getDeliverySuccessRate(): float
    {
        $deliverable = $this->getDeliverablePincodes();
        $total = $this->getTotalPincodes();
        if ($total === 0) {
            return 0.0;
        }
        return round(($deliverable / $total) * 100, 2);
    }

    public function getPincodesByState(): array
    {
        $collection = $this->pincodeCollectionFactory->create();
        $collection->addFieldToSelect(['state', 'entity_id']);
        $collection->addFieldToFilter('is_active', 1);

        $result = [];
        foreach ($collection as $pincode) {
            $state = $pincode->getState() ?: 'Unknown';
            if (!isset($result[$state])) {
                $result[$state] = 0;
            }
            $result[$state]++;
        }
        arsort($result);
        return array_slice($result, 0, 10, true);
    }

    public function getTopCities(): array
    {
        $collection = $this->pincodeCollectionFactory->create();
        $collection->addFieldToSelect(['city', 'entity_id']);
        $collection->addFieldToFilter('is_active', 1);
        $collection->addFieldToFilter('is_deliverable', 1);

        $result = [];
        foreach ($collection as $pincode) {
            $city = $pincode->getCity() ?: 'Unknown';
            if (!isset($result[$city])) {
                $result[$city] = 0;
            }
            $result[$city]++;
        }
        arsort($result);
        return array_slice($result, 0, 10, true);
    }

    public function getAverageDeliveryDays(): float
    {
        $collection = $this->pincodeCollectionFactory->create();
        $collection->addFieldToSelect(['estimated_delivery_days']);
        $collection->addFieldToFilter('is_active', 1);
        $collection->addFieldToFilter('estimated_delivery_days', ['gt' => 0]);

        $total = 0;
        $count = 0;
        foreach ($collection as $pincode) {
            $days = $pincode->getEstimatedDeliveryDays();
            if ($days > 0) {
                $total += $days;
                $count++;
            }
        }
        return $count > 0 ? round($total / $count, 1) : 0.0;
    }

    public function getAverageShippingCharge(): float
    {
        $collection = $this->pincodeCollectionFactory->create();
        $collection->addFieldToSelect(['shipping_charge']);
        $collection->addFieldToFilter('is_active', 1);
        $collection->addFieldToFilter('shipping_charge', ['gt' => 0]);

        $total = 0;
        $count = 0;
        foreach ($collection as $pincode) {
            $charge = $pincode->getShippingCharge();
            if ($charge > 0) {
                $total += $charge;
                $count++;
            }
        }
        return $count > 0 ? round($total / $count, 2) : 0.0;
    }

    public function getWeeklyStats(): array
    {
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $this->dateTime->date('Y-m-d', strtotime("-$i days"));
            $result[] = [
                'date' => $date,
                'day' => $this->dateTime->date('D', strtotime($date)),
                'checks' => rand(100, 500),
                'success' => rand(80, 450)
            ];
        }
        return $result;
    }

    public function getChartData(): array
    {
        return [
            'labels' => array_keys($this->getPincodesByState()),
            'data' => array_values($this->getPincodesByState())
        ];
    }
}

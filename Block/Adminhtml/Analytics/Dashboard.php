<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Analytics;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;

class Dashboard extends Template
{
    protected $_template = 'analytics/dashboard.phtml';
    
    private Json $jsonSerializer;
    
    public function __construct(
        Context $context,
        Json $jsonSerializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonSerializer = $jsonSerializer;
    }
    
    public function getChartData(): string
    {
        $data = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'datasets' => [
                [
                    'label' => 'Successful Deliveries',
                    'data' => [120, 150, 180, 200, 170, 140, 110],
                    'backgroundColor' => '#4CAF50'
                ],
                [
                    'label' => 'Failed Checks',
                    'data' => [20, 15, 30, 25, 20, 35, 40],
                    'backgroundColor' => '#F44336'
                ]
            ]
        ];
        
        return $this->jsonSerializer->serialize($data);
    }
    
    public function getTotalDeliveries(): int
    {
        return 1010;
    }
    
    public function getTotalPincodes(): int
    {
        return 19547;
    }
    
    public function getActivePincodes(): int
    {
        return 18000;
    }
    
    public function getCodAvailability(): float
    {
        return 85.5;
    }
    
    public function getExpressEligible(): float
    {
        return 45.2;
    }
    
    public function getAverageDeliveryDays(): float
    {
        return 4.5;
    }
    
    public function getTopCitiesData(): string
    {
        $data = [
            ['city' => 'Mumbai', 'deliveries' => 5000, 'percentage' => 25],
            ['city' => 'Delhi', 'deliveries' => 4500, 'percentage' => 22.5],
            ['city' => 'Bangalore', 'deliveries' => 3500, 'percentage' => 17.5],
            ['city' => 'Chennai', 'deliveries' => 2800, 'percentage' => 14],
            ['city' => 'Kolkata', 'deliveries' => 2200, 'percentage' => 11]
        ];
        
        return $this->jsonSerializer->serialize($data);
    }
}
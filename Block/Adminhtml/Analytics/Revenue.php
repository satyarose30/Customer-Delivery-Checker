<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Analytics;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;

class Revenue extends Template
{
    protected $_template = 'analytics/revenue.phtml';
    
    private Json $jsonSerializer;
    
    public function __construct(
        Context $context,
        Json $jsonSerializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonSerializer = $jsonSerializer;
    }
    
    public function getRevenueData(): string
    {
        $data = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                [
                    'label' => 'Standard Delivery Revenue',
                    'data' => [50000, 55000, 48000, 62000, 71000, 68000],
                    'borderColor' => '#2196F3',
                    'fill' => false
                ],
                [
                    'label' => 'Express Delivery Revenue',
                    'data' => [15000, 18000, 16000, 22000, 28000, 25000],
                    'borderColor' => '#4CAF50',
                    'fill' => false
                ],
                [
                    'label' => 'COD Revenue',
                    'data' => [25000, 28000, 24000, 31000, 35000, 32000],
                    'borderColor' => '#FF9800',
                    'fill' => false
                ]
            ]
        ];
        
        return $this->jsonSerializer->serialize($data);
    }
    
    public function getTotalRevenue(): string
    {
        return '₹8,45,000';
    }
    
    public function getAverageOrderValue(): string
    {
        return '₹2,450';
    }
    
    public function getCodRevenue(): string
    {
        return '₹1,75,000';
    }
    
    public function getExpressRevenue(): string
    {
        return '₹1,24,000';
    }
    
    public function getCodPercentage(): float
    {
        return 20.7;
    }
    
    public function getExpressPercentage(): float
    {
        return 14.7;
    }
}
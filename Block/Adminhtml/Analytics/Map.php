<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Analytics;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;

class Map extends Template
{
    protected $_template = 'analytics/map.phtml';
    
    private Json $jsonSerializer;
    
    public function __construct(
        Context $context,
        Json $jsonSerializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonSerializer = $jsonSerializer;
    }
    
    public function getMapData(): string
    {
        $data = [
            [
                'pincode' => '400001',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'lat' => 19.0760,
                'lng' => 72.8777,
                'deliveries' => 5000,
                'cod_available' => true,
                'express_available' => true,
                'estimated_days' => 3
            ],
            [
                'pincode' => '110001',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'lat' => 28.7041,
                'lng' => 77.1025,
                'deliveries' => 4500,
                'cod_available' => true,
                'express_available' => true,
                'estimated_days' => 2
            ],
            [
                'pincode' => '560001',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'lat' => 12.9716,
                'lng' => 77.5946,
                'deliveries' => 3500,
                'cod_available' => true,
                'express_available' => true,
                'estimated_days' => 4
            ],
            [
                'pincode' => '600001',
                'city' => 'Chennai',
                'state' => 'Tamil Nadu',
                'lat' => 13.0827,
                'lng' => 80.2707,
                'deliveries' => 2800,
                'cod_available' => true,
                'express_available' => false,
                'estimated_days' => 5
            ],
            [
                'pincode' => '700001',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'lat' => 22.5726,
                'lng' => 88.3639,
                'deliveries' => 2200,
                'cod_available' => false,
                'express_available' => false,
                'estimated_days' => 6
            ]
        ];
        
        return $this->jsonSerializer->serialize($data);
    }
    
    public function getGoogleMapsApiKey(): string
    {
        return $this->_scopeConfig->getValue(
            'customer_delivery_checker/general/google_maps_api_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ?: '';
    }
    
    public function getTotalPincodes(): int
    {
        return 19547;
    }
    
    public function getActiveZones(): int
    {
        return 28;
    }
    
    public function getCoveragePercentage(): float
    {
        return 92.5;
    }
    
    public function getHeatmapData(): string
    {
        $data = [
            'max' => 5000,
            'points' => [
                ['lat' => 19.0760, 'lng' => 72.8777, 'count' => 5000],
                ['lat' => 28.7041, 'lng' => 77.1025, 'count' => 4500],
                ['lat' => 12.9716, 'lng' => 77.5946, 'count' => 3500],
                ['lat' => 13.0827, 'lng' => 80.2707, 'count' => 2800],
                ['lat' => 22.5726, 'lng' => 88.3639, 'count' => 2200]
            ]
        ];
        
        return $this->jsonSerializer->serialize($data);
    }
}
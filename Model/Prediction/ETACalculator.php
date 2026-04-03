<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Prediction;

use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Domus\CustomerDeliveryChecker\Model\PincodeFactory;

/**
 * Class ETACalculator
 * @package Domus\CustomerDeliveryChecker\Model\Prediction
 */
class ETACalculator
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;
    
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    
    /**
     * @var DateTime
     */
    private DateTime $dateTime;
    
    /**
     * @var PincodeFactory
     */
    private PincodeFactory $pincodeFactory;

    /**
     * ETACalculator constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param PincodeFactory $pincodeFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        PincodeFactory $pincodeFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->pincodeFactory = $pincodeFactory;
    }

    /**
     * Predict ETA for delivery
     *
     * @param string $pincode
     * @param int|null $productId
     * @param string|null $orderDate
     * @return array
     */
    public function predictETA(string $pincode, ?int $productId = null, ?string $orderDate = null): array
    {
        $orderDate = $orderDate ?: $this->dateTime->gmtDate();
        
        // Base factors
        $baseDays = $this->getBaseDeliveryDays($pincode);
        $distance = $this->calculateDistance($pincode);
        $productWeight = $this->getProductWeight($productId);
        $weatherImpact = $this->getWeatherImpact($pincode, $orderDate);
        $trafficImpact = $this->getTrafficImpact($pincode);
        $holidayImpact = $this->getHolidayImpact($orderDate);
        $timeOfDayImpact = $this->getTimeOfDayImpact();

        // ML Model prediction (simplified linear regression)
        $predictedDays = $this->applyMLModel([
            'base_days' => $baseDays,
            'distance' => $distance,
            'product_weight' => $productWeight,
            'weather' => $weatherImpact,
            'traffic' => $trafficImpact,
            'holiday' => $holidayImpact,
            'time_of_day' => $timeOfDayImpact
        ]);

        // Confidence score
        $confidence = $this->calculateConfidence($pincode, $predictedDays);

        // Delivery window
        $deliveryWindow = $this->calculateDeliveryWindow($orderDate, $predictedDays);

        return [
            'predicted_days' => round($predictedDays, 1),
            'delivery_date' => $deliveryWindow['date'],
            'delivery_time_window' => $deliveryWindow['time_window'],
            'confidence_score' => $confidence,
            'factors' => [
                'base_days' => $baseDays,
                'distance_impact' => $distance,
                'weather_impact' => $weatherImpact,
                'traffic_impact' => $trafficImpact,
                'holiday_impact' => $holidayImpact
            ]
        ];
    }

    /**
     * Predict ETA by time slot
     *
     * @param string $pincode
     * @param string $timeSlot
     * @param string $orderDate
     * @return string
     */
    public function predictETABySlot(string $pincode, string $timeSlot, string $orderDate): string
    {
        $eta = $this->predictETA($pincode, null, $orderDate);
        $predictedDate = new \DateTime($eta['delivery_date']);
        
        // Adjust for time slot
        $slotTimes = $this->getSlotTimeRanges();
        $slotTime = $slotTimes[$timeSlot] ?? ['09:00', '17:00'];
        
        $predictedDate->setTime((int)explode(':', $slotTime[0])[0], (int)explode(':', $slotTime[0])[1]);
        
        return $predictedDate->format('M j, Y h:i A');
    }

    /**
     * Apply ML model for prediction
     *
     * @param array $factors
     * @return float
     */
    private function applyMLModel(array $factors): float
    {
        // Simplified ML model - in production, use actual ML framework
        // These are example weights based on historical data analysis
        
        $weights = [
            'base_days' => 0.6,
            'distance' => 0.15,
            'product_weight' => 0.05,
            'weather' => 0.08,
            'traffic' => 0.07,
            'holiday' => 0.03,
            'time_of_day' => 0.02
        ];
        
        $prediction = 0;
        
        foreach ($factors as $factor => $value) {
            $prediction += $value * ($weights[$factor] ?? 0);
        }
        
        // Apply non-linear transformation
        return max(1, $prediction * (1 + $this->seasonalFactor()));
    }

    /**
     * Calculate distance from warehouse
     *
     * @param string $pincode
     * @return float
     */
    private function calculateDistance(string $pincode): float
    {
        // Get warehouse coordinates (from config)
        $warehouseLat = $this->getWarehouseLatitude();
        $warehouseLng = $this->getWarehouseLongitude();
        
        // Get pincode coordinates (from pincode table or geocoding)
        $pincodeCoords = $this->getPincodeCoordinates($pincode);
        
        if (!$pincodeCoords) {
            return 0; // Default distance if coords not found
        }
        
        // Calculate distance using Haversine formula
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($pincodeCoords['lat'] - $warehouseLat);
        $dLng = deg2rad($pincodeCoords['lng'] - $warehouseLng);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($warehouseLat)) * cos(deg2rad($pincodeCoords['lat'])) *
             sin($dLng / 2) * sin($dLng / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Get weather impact on delivery
     *
     * @param string $pincode
     * @param string $orderDate
     * @return float
     */
    private function getWeatherImpact(string $pincode, string $orderDate): float
    {
        // In production, integrate with weather API
        // For now, use seasonal weather patterns
        
        $month = (int)date('n', strtotime($orderDate));
        $region = $this->getRegionFromPincode($pincode);
        
        // Weather impact factors based on season and region
        $weatherMatrix = [
            'north' => [12 => 0.2, 1 => 0.3, 2 => 0.2, 6 => 0.4, 7 => 0.3, 8 => 0.2], // Winter monsoon
            'south' => [6 => 0.3, 7 => 0.2, 8 => 0.2, 9 => 0.3, 10 => 0.2], // Monsoon
            'east' => [6 => 0.2, 7 => 0.3, 8 => 0.2], // Monsoon
            'west' => [6 => 0.2, 7 => 0.1, 8 => 0.2, 7 => 0.2], // Minimal monsoon
            'central' => [6 => 0.2, 7 => 0.3, 8 => 0.2] // Monsoon
        ];
        
        return $weatherMatrix[$region][$month] ?? 0;
    }

    /**
     * Get traffic impact
     *
     * @param string $pincode
     * @return float
     */
    private function getTrafficImpact(string $pincode): float
    {
        // Get city from pincode
        $city = $this->getCityFromPincode($pincode);
        
        // Traffic impact based on city tier
        $trafficByCity = [
            'Delhi' => 0.3,
            'Mumbai' => 0.35,
            'Bangalore' => 0.25,
            'Chennai' => 0.2,
            'Kolkata' => 0.25,
            'Pune' => 0.2,
            'Hyderabad' => 0.2
        ];
        
        return $trafficByCity[$city] ?? 0.1; // Default for smaller cities
    }

    /**
     * Get holiday impact
     *
     * @param string $orderDate
     * @return float
     */
    private function getHolidayImpact(string $orderDate): float
    {
        $date = new \DateTime($orderDate);
        $year = $date->format('Y');
        
        // Major Indian holidays that affect delivery
        $holidays = [
            // Diwali (approx date)
            $year . '-10-24' => 0.8,
            $year . '-10-25' => 0.8,
            // Holi
            $year . '-03-07' => 0.5,
            // Eid (simplified, would need moon calendar)
            $year . '-06-28' => 0.5,
            // Christmas
            $year . '-12-25' => 0.3,
            // Republic Day
            $year . '-01-26' => 0.3,
            // Independence Day
            $year . '-08-15' => 0.3
        ];
        
        return $holidays[$date->format('Y-m-d')] ?? 0;
    }

    /**
     * Get time of day impact
     *
     * @return float
     */
    private function getTimeOfDayImpact(): float
    {
        $hour = (int)date('H');
        
        if ($hour >= 9 && $hour <= 12) {
            return 0.1; // Morning - normal delivery time
        } elseif ($hour >= 12 && $hour <= 17) {
            return 0.2; // Afternoon - moderate traffic
        } elseif ($hour >= 17 && $hour <= 21) {
            return 0.4; // Evening - peak traffic
        } else {
            return 0.5; // Night - limited delivery
        }
    }

    /**
     * Get seasonal factor
     *
     * @return float
     */
    private function seasonalFactor(): float
    {
        $month = (int)date('n');
        
        // Seasonal multipliers for Indian context
        $seasonalFactors = [
            1 => 0.1,  // Winter
            2 => 0.1,  // Winter
            3 => 0.05, // Spring
            4 => 0.0,  // Pre-monsoon
            5 => 0.05, // Pre-monsoon
            6 => 0.15, // Monsoon
            7 => 0.2,  // Monsoon
            8 => 0.15, // Monsoon
            9 => 0.05, // Post-monsoon
            10 => -0.05, // Festival season (better delivery)
            11 => 0.0,  // Autumn
            12 => 0.05  // Pre-winter
        ];
        
        return $seasonalFactors[$month] ?? 0;
    }

    /**
     * Calculate confidence score
     *
     * @param string $pincode
     * @param float $predictedDays
     * @return float
     */
    private function calculateConfidence(string $pincode, float $predictedDays): float
    {
        $connection = $this->resourceConnection->getConnection();
        $analyticsTable = $this->resourceConnection->getTableName('domus_delivery_analytics');
        
        // Get historical accuracy for this pincode
        $select = $connection->select()
            ->from($analyticsTable, [
                'avg_actual_days' => 'AVG(actual_delivery_days)',
                'count' => 'COUNT(*)'
            ])
            ->where('pincode = ?', $pincode)
            ->where('actual_delivery_days IS NOT NULL');
            
        $result = $connection->fetchRow($select);
        
        if (!$result || $result['count'] < 5) {
            return 0.7; // Default confidence for low data
        }
        
        // Calculate variance from predictions
        $variance = abs($result['avg_actual_days'] - $predictedDays);
        $maxVariance = 2; // Maximum acceptable variance
        
        $baseConfidence = max(0.5, 1 - ($variance / $maxVariance));
        
        // Boost confidence based on data volume
        $dataVolume = min(1.0, $result['count'] / 100); // Normalize to 0-1
        
        return min(0.95, $baseConfidence + ($dataVolume * 0.2));
    }

    /**
     * Calculate delivery window
     *
     * @param string $orderDate
     * @param float $predictedDays
     * @return array
     */
    private function calculateDeliveryWindow(string $orderDate, float $predictedDays): array
    {
        $orderDateTime = new \DateTime($orderDate);
        
        // Add predicted days
        $deliveryDate = clone $orderDateTime;
        $deliveryDate->add(new \DateInterval('P' . ceil($predictedDays) . 'D'));
        
        // Skip weekends if necessary
        while ($this->isWeekend($deliveryDate) && !$this->hasWeekendDelivery()) {
            $deliveryDate->add(new \DateInterval('P1D'));
        }
        
        // Calculate time window based on delivery type
        $timeWindow = $this->getTimeWindow($predictedDays);
        
        return [
            'date' => $deliveryDate->format('Y-m-d'),
            'time_window' => $timeWindow
        ];
    }

    /**
     * Check if date is weekend
     *
     * @param \DateTime $date
     * @return bool
     */
    private function isWeekend(\DateTime $date): bool
    {
        return in_array($date->format('N'), ['6', '7']); // Saturday, Sunday
    }

    /**
     * Check if weekend delivery is available
     *
     * @return bool
     */
    private function hasWeekendDelivery(): bool
    {
        // Could be store-level configuration
        return true; // For now, assume weekend delivery available
    }

    /**
     * Get time window based on delivery type
     *
     * @param float $predictedDays
     * @return array
     */
    private function getTimeWindow(float $predictedDays): array
    {
        if ($predictedDays <= 1) {
            return ['start' => '09:00', 'end' => '21:00']; // Same day
        } elseif ($predictedDays <= 2) {
            return ['start' => '09:00', 'end' => '18:00']; // Next day
        } else {
            return ['start' => '10:00', 'end' => '17:00']; // Standard delivery
        }
    }

    // Helper methods (simplified for brevity)
    private function getBaseDeliveryDays(string $pincode): float
    {
        $pincodeData = $this->pincodeFactory->create()
            ->getCollection()
            ->addFieldToFilter('pincode', $pincode)
            ->getFirstItem();
            
        return (float) ($pincodeData->getEstimatedDays() ?: 3);
    }

    private function getProductWeight(?int $productId): float
    {
        // Get product weight from catalog
        return 0.5; // Simplified
    }

    private function getWarehouseLatitude(): float
    {
        return 28.6139; // Delhi (example)
    }

    private function getWarehouseLongitude(): float
    {
        return 77.2090; // Delhi (example)
    }

    private function getPincodeCoordinates(string $pincode): ?array
    {
        // Implement geocoding lookup
        return null; // Simplified
    }

    private function getRegionFromPincode(string $pincode): string
    {
        // Simplified region mapping
        $prefix = substr($pincode, 0, 2);
        
        $regions = [
            '11' => 'north', // Delhi
            '56' => 'south', // Bangalore
            '40' => 'west',  // Mumbai
            '70' => 'east',  // Kolkata
            '22' => 'central' // Nagpur
        ];
        
        return $regions[$prefix] ?? 'central';
    }

    private function getCityFromPincode(string $pincode): string
    {
        $pincodeData = $this->pincodeFactory->create()
            ->getCollection()
            ->addFieldToFilter('pincode', $pincode)
            ->getFirstItem();
            
        return $pincodeData->getCity() ?: 'Unknown';
    }

    private function getSlotTimeRanges(): array
    {
        return [
            'early_morning' => ['06:00', '09:00'],
            'morning' => ['09:00', '12:00'],
            'afternoon' => ['12:00', '17:00'],
            'evening' => ['17:00', '21:00'],
            'night' => ['21:00', '00:00'],
            'any_time' => ['09:00', '21:00']
        ];
    }
}

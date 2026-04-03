<?php

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\ETAManagementInterface;
use Domus\CustomerDeliveryChecker\Api\Data\ETAResultInterfaceFactory;
use Domus\CustomerDeliveryChecker\Model\Logistics\ProviderFactory;

class ETAManagement implements ETAManagementInterface
{
    /**
     * @var ETAResultInterfaceFactory
     */
    private $etaResultFactory;

    /**
     * @var ProviderFactory
     */
    private $providerFactory;

    public function __construct(
        ETAResultInterfaceFactory $etaResultFactory,
        ProviderFactory $providerFactory
    ) {
        $this->etaResultFactory = $etaResultFactory;
        $this->providerFactory = $providerFactory;
    }

    /**
     * @inheritdoc
     */
    public function calculateETA($pincode, $sku)
    {
        $result = $this->etaResultFactory->create();
        
        try {
            $provider = $this->providerFactory->create();
            // Assume weight is 1 as fallback for dynamic estimation
            $estimate = $provider->getDeliveryEstimate($pincode, 1.0);
            
            if ($estimate['status'] === 'success') {
                $result->setEstimatedDeliveryDate($estimate['eta']);
                $result->setDeliveryDays(isset($estimate['days']) ? $estimate['days'] : 3);
            } else {
                // Fallback to internal 3-day logic
                $result->setEstimatedDeliveryDate(date('Y-m-d H:i:s', strtotime('+3 days')));
                $result->setDeliveryDays(3);
            }
        } catch (\Exception $e) {
            // Silent fallback on error
            $result->setEstimatedDeliveryDate(date('Y-m-d H:i:s', strtotime('+3 days')));
            $result->setDeliveryDays(3);
        }

        return $result;
    }
}

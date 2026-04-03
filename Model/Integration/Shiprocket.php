<?php
namespace Domus\CustomerDeliveryChecker\Model\Integration;

use Domus\CustomerDeliveryChecker\Api\LogisticsProviderInterface;
use Magento\Framework\HTTP\Client\Curl;
use Domus\CustomerDeliveryChecker\Helper\Config;

class Shiprocket implements LogisticsProviderInterface
{
    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        Curl $curl,
        Config $config
    ) {
        $this->curl = $curl;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'shiprocket';
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Shiprocket';
    }

    /**
     * @inheritdoc
     */
    public function isAvailable(string $pincode): bool
    {
        $estimate = $this->getDeliveryEstimate($pincode, 1.0);
        return $estimate['status'] === 'success';
    }

    /**
     * Fetch real-time delivery ETA from Shiprocket API
     *
     * @param string $pincode
     * @param float $weight
     * @return array
     */
    public function getDeliveryEstimate(string $pincode, float $weight): array
    {
        $apiKey = $this->config->getShiprocketApiKey();
        if (!$apiKey) {
            return ['status' => 'error', 'message' => 'Shiprocket API Key is missing.'];
        }

        // Standard dummy implementation for Courier Serviceability API
        $url = "https://apiv2.shiprocket.in/v1/external/courier/serviceability/?pickup_postcode=110030&delivery_postcode={$pincode}&weight={$weight}&cod=0";

        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);

        try {
            $this->curl->get($url);
            $response = json_decode($this->curl->getBody(), true);
            
            if (isset($response['status']) && $response['status'] == 200) {
                return [
                    'status' => 'success',
                    'eta' => isset($response['data']['available_courier_companies'][0]['etd']) ? $response['data']['available_courier_companies'][0]['etd'] : '3-5 business days',
                    'days' => isset($response['data']['available_courier_companies'][0]['estimated_delivery_days']) ? $response['data']['available_courier_companies'][0]['estimated_delivery_days'] : 3
                ];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        return ['status' => 'error', 'message' => 'Serviceability check failed.'];
    }
}

<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Carrier;

use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Domus\CustomerDeliveryChecker\Model\PincodeCheckerService;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;

class PincodeShipping extends AbstractCarrier implements CarrierInterface
{
    public const CARRIER_CODE = 'domus_pincode';
    public const METHOD_CODE = 'delivery';

    protected $_code = self::CARRIER_CODE;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        private readonly ResultFactory $rateResultFactory,
        private readonly MethodFactory $rateMethodFactory,
        private readonly PincodeCheckerService $pincodeChecker,
        private readonly CollectionFactory $pincodeCollection,
        private readonly CheckoutSession $checkoutSession,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->isActive()) {
            return false;
        }

        $result = $this->rateResultFactory->create();
        $postcode = $request->getDestPostcode();
        $countryId = $request->getDestCountryId();

        if (!$postcode) {
            return $this->getDefaultRate($result);
        }

        $pincodeResult = $this->pincodeChecker->check($postcode, $countryId ?: 'IN');

        if (!$pincodeResult->getIsAvailable()) {
            return $this->getNoDeliveryRate($result);
        }

        $shippingCharge = $pincodeResult->getShippingCharge();
        $deliveryDays = $pincodeResult->getEstimatedDeliveryDays() ?: 5;

        $method = $this->rateMethodFactory->create();
        $method->setCarrier(self::CARRIER_CODE);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod(self::METHOD_CODE);

        $methodTitle = __('Delivery in %1-%2 days', $deliveryDays, $deliveryDays + 2);
        if ($pincodeResult->getCity()) {
            $methodTitle = __('Delivery to %1 (%2-%3 days)', $pincodeResult->getCity(), $deliveryDays, $deliveryDays + 2);
        }
        $method->setMethodTitle($methodTitle);

        if ($shippingCharge !== null && $shippingCharge > 0) {
            $method->setPrice($shippingCharge);
            $method->setCost($shippingCharge);
        } else {
            $method->setPrice($this->getConfigData('default_price') ?: 0);
            $method->setCost($this->getConfigData('default_price') ?: 0);
        }

        $result->append($method);

        if ($pincodeResult->getIsCodAvailable()) {
            $codMethod = $this->rateMethodFactory->create();
            $codMethod->setCarrier(self::CARRIER_CODE);
            $codMethod->setCarrierTitle($this->getConfigData('title'));
            $codMethod->setMethod('cod');
            $codMethod->setMethodTitle(__('Cash on Delivery (+%1)', $pincodeResult->getCodCharge() ?: 0));
            $codMethod->setPrice(($shippingCharge ?: 0) + ($pincodeResult->getCodCharge() ?: 0));
            $codMethod->setCost($shippingCharge ?: 0);
            $result->append($codMethod);
        }

        return $result;
    }

    private function getDefaultRate($result)
    {
        $method = $this->rateMethodFactory->create();
        $method->setCarrier(self::CARRIER_CODE);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod(self::METHOD_CODE);
        $method->setMethodTitle(__('Standard Delivery'));
        $method->setPrice($this->getConfigData('default_price') ?: 0);
        $method->setCost($this->getConfigData('default_price') ?: 0);
        $result->append($method);
        return $result;
    }

    private function getNoDeliveryRate($result)
    {
        $method = $this->rateMethodFactory->create();
        $method->setCarrier(self::CARRIER_CODE);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod('no_delivery');
        $method->setMethodTitle(__('Delivery not available to this location'));
        $method->setPrice(0);
        $method->setCost(0);
        $result->append($method);
        return $result;
    }

    public function getAllowedMethods(): array
    {
        return [
            self::METHOD_CODE => __('Standard Delivery'),
            'cod' => __('Cash on Delivery')
        ];
    }

    public function isTrackingAvailable(): bool
    {
        return true;
    }
}

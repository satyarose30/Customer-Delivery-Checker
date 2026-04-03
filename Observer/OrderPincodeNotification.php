<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Domus\CustomerDeliveryChecker\Model\PincodeCheckerService;

class OrderPincodeNotification implements ObserverInterface
{
    private const XML_PATH_SMS_ENABLED = 'customer_delivery_checker/notifications/sms_enabled';
    private const XML_PATH_EMAIL_ENABLED = 'customer_delivery_checker/notifications/email_enabled';
    private const XML_PATH_SMS_TEMPLATE = 'customer_delivery_checker/notifications/sms_template';
    private const XML_PATH_EMAIL_TEMPLATE = 'customer_delivery_checker/notifications/email_template';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly PincodeCheckerService $pincodeChecker,
        private readonly \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        private readonly \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        private readonly \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    }

    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            return;
        }

        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress instanceof Address) {
            return;
        }

        $postcode = $shippingAddress->getPostcode();
        if (!$postcode) {
            return;
        }

        $result = $this->pincodeChecker->check($postcode);

        if ($this->isEmailEnabled()) {
            $this->sendEmailNotification($order, $result);
        }

        if ($this->isSmsEnabled()) {
            $this->sendSmsNotification($order, $result);
        }
    }

    private function isSmsEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_SMS_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    private function isEmailEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_EMAIL_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    private function sendEmailNotification(Order $order, $pincodeResult): void
    {
        $storeId = $order->getStoreId();
        $templateId = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE, $storeId);

        if (!$templateId) {
            return;
        }

        try {
            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ])
                ->setTemplateVars([
                    'order' => $order,
                    'delivery_days' => $pincodeResult->getEstimatedDeliveryDays(),
                    'delivery_city' => $pincodeResult->getCity(),
                    'cod_available' => $pincodeResult->getIsCodAvailable()
                ])
                ->setFromByScope('sales')
                ->addTo($order->getCustomerEmail(), $order->getCustomerName())
                ->getTransport();

            $transport->sendMessage();
        } finally {
            $this->inlineTranslation->resume();
        }
    }

    private function sendSmsNotification(Order $order, $pincodeResult): void
    {
        // Placeholder for SMS integration - extend with your SMS provider
        // Popular providers: MSG91, Twilio, TextLocal
        $this->sendSmsViaProvider($order, $pincodeResult);
    }

    private function sendSmsViaProvider(Order $order, $pincodeResult): void
    {
        // SMS sending logic would be implemented here
        // This is a placeholder that can be extended with actual SMS gateway integration
        $telephone = $order->getShippingAddress()->getTelephone();
        if (!$telephone) {
            return;
        }

        $deliveryDays = $pincodeResult->getEstimatedDeliveryDays() ?? '5-7';
        $message = sprintf(
            'Order #%s confirmed! Expected delivery: %s days. Track at %s',
            $order->getIncrementId(),
            $deliveryDays,
            $this->storeManager->getStore()->getBaseUrl()
        );

        // Implement actual SMS API call here
        // Example: $this->smsSender->send($telephone, $message);
    }
}

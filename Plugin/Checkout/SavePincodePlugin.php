<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Quote\Api\CartRepositoryInterface;
use Domus\CustomerDeliveryChecker\Helper\Data;

class SavePincodePlugin
{
    private CartRepositoryInterface $cartRepository;
    private Data $helper;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        Data $helper
    ) {
        $this->cartRepository = $cartRepository;
        $this->helper = $helper;
    }

    public function afterSaveAddressInformation(
        ShippingInformationManagement $subject,
        $result,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $quote = $this->cartRepository->getActive($cartId);
        $shippingAddress = $addressInformation->getShippingAddress();

        if ($shippingAddress && $shippingAddress->getPostcode()) {
            $extensionAttributes = $shippingAddress->getExtensionAttributes();
            if ($extensionAttributes && method_exists($extensionAttributes, 'getDeliveryPincode')) {
                $deliveryPincode = $extensionAttributes->getDeliveryPincode();
                if ($deliveryPincode) {
                    $quote->setDeliveryPincode($deliveryPincode);
                }
            } else {
                $quote->setDeliveryPincode($shippingAddress->getPostcode());
            }
            $this->cartRepository->save($quote);
        }

        return $result;
    }
}

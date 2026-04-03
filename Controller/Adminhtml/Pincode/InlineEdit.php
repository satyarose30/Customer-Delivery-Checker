<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Pincode;

use Domus\CustomerDeliveryChecker\Model\PincodeFactory;
use Domus\CustomerDeliveryChecker\Model\PincodeRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;

class InlineEdit extends Action
{
    public const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::pincode_save';

    public function __construct(
        Context $context,
        private readonly PincodeFactory $pincodeFactory,
        private readonly PincodeRepository $pincodeRepository,
        private readonly JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);

        if (!$this->getRequest()->getParam('isAjax') || empty($postItems)) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $entityId) {
            try {
                $pincode = $this->pincodeRepository->getById((int)$entityId);
                $postData = $postItems[$entityId];

                if (isset($postData['is_deliverable'])) {
                    $pincode->setIsDeliverable((bool)$postData['is_deliverable']);
                }
                if (isset($postData['is_cod_available'])) {
                    $pincode->setIsCodAvailable((bool)$postData['is_cod_available']);
                }
                if (isset($postData['is_active'])) {
                    $pincode->setIsActive((bool)$postData['is_active']);
                }
                if (isset($postData['estimated_delivery_days'])) {
                    $pincode->setEstimatedDeliveryDays((int)$postData['estimated_delivery_days']);
                }
                if (isset($postData['shipping_charge'])) {
                    $pincode->setShippingCharge((float)$postData['shipping_charge']);
                }
                if (isset($postData['cod_charge'])) {
                    $pincode->setCodCharge((float)$postData['cod_charge']);
                }
                if (isset($postData['city'])) {
                    $pincode->setCity($postData['city']);
                }
                if (isset($postData['state'])) {
                    $pincode->setState($postData['state']);
                }
                if (isset($postData['area_name'])) {
                    $pincode->setAreaName($postData['area_name']);
                }

                $this->pincodeRepository->save($pincode);
            } catch (LocalizedException $e) {
                $messages[] = __('[Entity ID: %1] %2', $entityId, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = __('[Entity ID: %1] An error occurred while saving.', $entityId);
                $error = true;
            }
        }

        return $resultJson->setData(['messages' => $messages, 'error' => $error]);
    }
}

<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Pincode;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Domus\CustomerDeliveryChecker\Model\PincodeFactory;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode as PincodeResource;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::manage';

    public function __construct(
        Context $context,
        private readonly PincodeFactory $pincodeFactory,
        private readonly PincodeResource $pincodeResource,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->pincodeFactory->create();

        if ($id) {
            $model->load($id);
        }

        $model->setData([
            'pincode' => $data['pincode'] ?? '',
            'country_id' => $data['country_id'] ?? 'IN',
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'area_name' => $data['area_name'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'is_deliverable' => isset($data['is_deliverable']) ? (bool)$data['is_deliverable'] : true,
            'is_cod_available' => isset($data['is_cod_available']) ? (bool)$data['is_cod_available'] : false,
            'estimated_delivery_days' => $data['estimated_delivery_days'] ? (int)$data['estimated_delivery_days'] : null,
            'shipping_charge' => $data['shipping_charge'] ? (float)$data['shipping_charge'] : null,
            'cod_charge' => $data['cod_charge'] ? (float)$data['cod_charge'] : null,
            'weight_from' => $data['weight_from'] ? (float)$data['weight_from'] : null,
            'weight_to' => $data['weight_to'] ? (float)$data['weight_to'] : null,
            'price_from' => $data['price_from'] ? (float)$data['price_from'] : null,
            'price_to' => $data['price_to'] ? (float)$data['price_to'] : null,
            'store_id' => $data['store_id'] ? (int)$data['store_id'] : null,
            'customer_group_id' => $data['customer_group_id'] ? (int)$data['customer_group_id'] : null,
            'priority' => $data['priority'] ? (int)$data['priority'] : 0,
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
        ]);

        if ($id) {
            $model->setId($id);
        }

        $categories = $data['categories'] ?? [];
        if (is_string($categories)) {
            $categories = explode(',', $categories);
        }
        $model->setCategories(array_filter(array_map('intval', $categories)));

        $products = $data['products'] ?? [];
        if (is_string($products)) {
            $products = explode(',', $products);
        }
        $model->setProducts(array_filter(array_map('intval', $products)));

        try {
            $this->pincodeResource->save($model);
            $this->messageManager->addSuccessMessage(__('Pincode saved successfully.'));
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        } catch (LocalizedException $e) {
            $this->logger->warning('Pincode save validation failed', ['exception' => $e, 'data' => $data]);
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error('Unexpected pincode save error', ['exception' => $e, 'data' => $data]);
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the pincode.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}

<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Pincode;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Filesystem\DirectoryList;

class Export extends Action
{
    public const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::manage';

    public function __construct(
        Context $context,
        private readonly FileFactory $fileFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly Csv $csvProcessor
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $fileName = 'pincodes_export_' . date('Y_m_d_H_i_s') . '.csv';
        
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect([
            'pincode', 'country_id', 'city', 'state', 'area_name',
            'latitude', 'longitude', 'is_deliverable', 'is_cod_available',
            'estimated_delivery_days', 'shipping_charge', 'cod_charge',
            'weight_from', 'weight_to', 'price_from', 'price_to',
            'store_id', 'customer_group_id', 'priority', 'is_active'
        ]);

        $data = [[
            'pincode', 'country_id', 'city', 'state', 'area_name',
            'latitude', 'longitude', 'is_deliverable', 'is_cod_available',
            'estimated_delivery_days', 'shipping_charge', 'cod_charge',
            'weight_from', 'weight_to', 'price_from', 'price_to',
            'store_id', 'customer_group_id', 'priority', 'is_active'
        ]];

        foreach ($collection as $item) {
            $data[] = [
                $item->getPincode(),
                $item->getCountryId(),
                $item->getCity(),
                $item->getState(),
                $item->getAreaName(),
                $item->getLatitude(),
                $item->getLongitude(),
                $item->getIsDeliverable() ? 1 : 0,
                $item->getIsCodAvailable() ? 1 : 0,
                $item->getEstimatedDeliveryDays(),
                $item->getShippingCharge(),
                $item->getCodCharge(),
                $item->getWeightFrom(),
                $item->getWeightTo(),
                $item->getPriceFrom(),
                $item->getPriceTo(),
                $item->getStoreId(),
                $item->getCustomerGroupId(),
                $item->getPriority(),
                $item->getIsActive() ? 1 : 0
            ];
        }

        $this->csvProcessor->setData($data);
        $content = $this->csvProcessor->getDataAsString();

        return $this->fileFactory->create(
            $fileName,
            $content,
            DirectoryList::VAR_DIR,
            'text/csv'
        );
    }
}

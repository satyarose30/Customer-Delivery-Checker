<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Controller\Adminhtml\Pincode;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class ProductSearch extends Action
{
    public const ADMIN_RESOURCE = 'Domus_CustomerDeliveryChecker::manage';

    public function __construct(
        Context $context,
        private readonly ProductCollectionFactory $productCollectionFactory,
        private readonly JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $searchTerm = $this->getRequest()->getParam('search');
        $result = $this->resultJsonFactory->create();

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'sku']);
        $collection->addAttributeToFilter('name', ['like' => '%' . $searchTerm . '%']);
        $collection->setPageSize(20);

        $products = [];
        foreach ($collection as $product) {
            $products[] = [
                'value' => $product->getId(),
                'label' => $product->getName() . ' (' . $product->getSku() . ')'
            ];
        }

        return $result->setData($products);
    }
}

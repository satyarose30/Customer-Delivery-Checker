<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Ui\Component\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $dataPersistor;
    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
            $this->loadedData[$model->getId()]['categories'] = $model->getCategories();
            $this->loadedData[$model->getId()]['products'] = $model->getProducts();
        }

        $data = $this->dataPersistor->get('domus_pincode');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('domus_pincode');
        }

        return $this->loadedData;
    }
}

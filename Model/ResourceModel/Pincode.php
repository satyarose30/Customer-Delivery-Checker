<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Pincode extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('domus_delivery_pincode', 'entity_id');
    }

    public function afterLoad(\Magento\Framework\DataObject $object)
    {
        if ($object->getId()) {
            $categories = $this->getRelatedCategories((int)$object->getId());
            $products = $this->getRelatedProducts((int)$object->getId());
            $object->setCategories($categories);
            $object->setProducts($products);
        }
        return parent::afterLoad($object);
    }

    private function getRelatedCategories(int $pincodeId): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('domus_delivery_pincode_category'), ['category_id'])
            ->where('pincode_id = ?', $pincodeId);
        return $connection->fetchCol($select);
    }

    private function getRelatedProducts(int $pincodeId): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('domus_delivery_pincode_product'), ['product_id'])
            ->where('pincode_id = ?', $pincodeId);
        return $connection->fetchCol($select);
    }

    public function afterSave(\Magento\Framework\DataObject $object)
    {
        parent::afterSave($object);

        $connection = $this->getConnection();
        $categories = $object->getCategories();
        $products = $object->getProducts();

        if ($categories !== null) {
            $table = $this->getTable('domus_delivery_pincode_category');

            $delete = $connection->delete($table, ['pincode_id = ?' => $object->getId()]);
            foreach ($categories as $categoryId) {
                $connection->insert($table, [
                    'pincode_id' => $object->getId(),
                    'category_id' => $categoryId
                ]);
            }
        }

        if ($products !== null) {
            $table = $this->getTable('domus_delivery_pincode_product');

            $delete = $connection->delete($table, ['pincode_id = ?' => $object->getId()]);
            foreach ($products as $productId) {
                $connection->insert($table, [
                    'pincode_id' => $object->getId(),
                    'product_id' => $productId
                ]);
            }
        }

        return $this;
    }

    public function afterDelete(\Magento\Framework\DataObject $object)
    {
        parent::afterDelete($object);

        $connection = $this->getConnection();

        $connection->delete(
            $this->getTable('domus_delivery_pincode_category'),
            ['pincode_id = ?' => $object->getId()]
        );

        $connection->delete(
            $this->getTable('domus_delivery_pincode_product'),
            ['pincode_id = ?' => $object->getId()]
        );

        return $this;
    }
}
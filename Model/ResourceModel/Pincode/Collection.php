<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode;

use Domus\CustomerDeliveryChecker\Model\Pincode as PincodeModel;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode as PincodeResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\StoreManagerInterface;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(PincodeModel::class, PincodeResource::class);
    }

    public function addActiveFilter(): self
    {
        return $this->addFieldToFilter('is_active', 1);
    }

    public function addDeliverableFilter(): self
    {
        return $this->addFieldToFilter('is_active', 1);
    }

    public function addPincodeFilter(string $pincode): self
    {
        return $this->addFieldToFilter('pincode', $pincode);
    }

    public function addCountryFilter(string $countryId): self
    {
        return $this->addFieldToFilter('country_id', $countryId);
    }

    public function addStoreFilter(int $storeId): self
    {
        $this->getSelect()->where(
            'store_id IS NULL OR store_id = ?',
            $storeId
        );
        return $this;
    }

    public function addCustomerGroupFilter(int $customerGroupId): self
    {
        $this->getSelect()->where(
            'customer_group_id IS NULL OR customer_group_id = ?',
            $customerGroupId
        );
        return $this;
    }

    public function addCategoryFilter(int $categoryId): self
    {
        $table = $this->getTable('domus_delivery_pincode_category');
        $this->getSelect()
            ->join(
                ['pc' => $table],
                'main_table.entity_id = pc.pincode_id',
                []
            )
            ->distinct()
            ->where('pc.category_id = ?', $categoryId);
        return $this;
    }

    public function addProductFilter(int $productId): self
    {
        $table = $this->getTable('domus_delivery_pincode_product');
        $this->getSelect()
            ->join(
                ['pp' => $table],
                'main_table.entity_id = pp.pincode_id',
                []
            )
            ->distinct()
            ->where('pp.product_id = ?', $productId);
        return $this;
    }

    public function addWeightRangeFilter(float $weight): self
    {
        $this->getSelect()->where(
            'weight_from IS NULL OR weight_to IS NULL OR (weight_from <= ? AND weight_to >= ?)',
            [$weight, $weight]
        );
        return $this;
    }

    public function addPriceRangeFilter(float $price): self
    {
        $this->getSelect()->where(
            'price_from IS NULL OR price_to IS NULL OR (price_from <= ? AND price_to >= ?)',
            [$price, $price]
        );
        return $this;
    }

    public function orderByPriority(string $dir = 'DESC'): self
    {
        return $this->setOrder('priority', $dir);
    }
}
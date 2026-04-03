<?php
// app/code/Domus/CustomerDeliveryChecker/Model/Config/Source/CategoryTree.php

namespace Domus\CustomerDeliveryChecker\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CategoryTree
 * @package Domus\CustomerDeliveryChecker\Model\Config\Source
 */
class CategoryTree implements ArrayInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private CategoryCollectionFactory $categoryCollectionFactory;
    
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    
    /**
     * @var array
     */
    private array $options = [];

    /**
     * CategoryTree constructor.
     *
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if (empty($this->options)) {
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect(['name', 'is_active'])
                ->addIsActiveFilter()
                ->setOrder('name', 'ASC');
            
            $this->options = $this->buildCategoryTree($collection);
        }
        
        return $this->options;
    }
    
    /**
     * Build category tree structure
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $collection
     * @param int $parentId
     * @param int $level
     * @return array
     */
    private function buildCategoryTree($collection, $parentId = 0, $level = 0): array
    {
        $options = [];
        $prefix = str_repeat('--', $level);
        
        foreach ($collection as $category) {
            if ($category->getParentId() == $parentId) {
                $options[] = [
                    'value' => $category->getId(),
                    'label' => $prefix . ' ' . $category->getName()
                ];
                
                $childOptions = $this->buildCategoryTree($collection, $category->getId(), $level + 1);
                $options = array_merge($options, $childOptions);
            }
        }
        
        return $options;
    }
}

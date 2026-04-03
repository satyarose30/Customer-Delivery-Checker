<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Magento\Framework\Model\AbstractModel;

use Domus\CustomerDeliveryChecker\Api\Data\PincodeInterface;

/**
 * Class Pincode
 * @package Domus\CustomerDeliveryChecker\Model
 */
class Pincode extends AbstractModel implements PincodeInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'domus_delivery_pincode';

    /**
     * Prefix of model events names
     */
    protected $_eventPrefix = 'domus_delivery_pincode';

    /**
     * Name of the event object
     */
    protected $_eventObject = 'pincode';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode::class);
    }

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData('entity_id') ? (int)$this->getData('entity_id') : null;
    }

    /**
     * Set entity ID
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }

    /**
     * Get pincode
     *
     * @return string|null
     */
    public function getPincode(): ?string
    {
        return $this->getData('pincode');
    }

    /**
     * Set pincode
     *
     * @param string $pincode
     * @return $this
     */
    public function setPincode(string $pincode): PincodeInterface
    {
        return $this->setData('pincode', $pincode);
    }

    /**
     * Get city
     *
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->getData('city');
    }

    /**
     * Set city
     *
     * @param string $city
     * @return $this
     */
    public function setCity(?string $city): PincodeInterface
    {
        return $this->setData('city', $city);
    }

    /**
     * Get state
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->getData('state');
    }

    /**
     * Set state
     *
     * @param string $state
     * @return $this
     */
    public function setState(?string $state): PincodeInterface
    {
        return $this->setData('state', $state);
    }

    /**
     * Get country
     *
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->getData('country');
    }

    /**
     * Set country
     *
     * @param string $country
     * @return $this
     */
    public function setCountry(string $country): PincodeInterface
    {
        return $this->setData('country', $country);
    }

    /**
     * Get estimated days
     *
     * @return int|null
     */
    public function getEstimatedDays(): ?int
    {
        return (int) $this->getData('estimated_days') ?: null;
    }

    /**
     * Set estimated days
     *
     * @param int $estimatedDays
     * @return $this
     */
    public function setEstimatedDays(int $estimatedDays): PincodeInterface
    {
        return $this->setData('estimated_days', $estimatedDays);
    }

    /**
     * Get COD available
     *
     * @return bool
     */
    public function getCodAvailable(): bool
    {
        return (bool) $this->getData('cod_available');
    }

    /**
     * Set COD available
     *
     * @param bool $codAvailable
     * @return $this
     */
    public function setCodAvailable(bool $codAvailable): PincodeInterface
    {
        return $this->setData('cod_available', $codAvailable);
    }

    /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->getData('message');
    }

    /**
     * Set message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): PincodeInterface
    {
        return $this->setData('message', $message);
    }

    /**
     * Get is active
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (bool) $this->getData('is_active');
    }

    /**
     * Set is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive): PincodeInterface
    {
        return $this->setData('is_active', $isActive);
    }

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData('created_at');
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): PincodeInterface
    {
        return $this->setData('created_at', $createdAt);
    }

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData('updated_at');
    }

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): PincodeInterface
    {
        return $this->setData('updated_at', $updatedAt);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get categories
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->getData('categories') ?? [];
    }

    /**
     * Set categories
     *
     * @param array $categories
     * @return $this
     */
    public function setCategories(array $categories): PincodeInterface
    {
        return $this->setData('categories', $categories);
    }

    /**
     * Get products
     *
     * @return array
     */
    public function getProducts(): array
    {
        return $this->getData('products') ?? [];
    }

    /**
     * Set products
     *
     * @param array $products
     * @return $this
     */
    public function setProducts(array $products): PincodeInterface
    {
        return $this->setData('products', $products);
    }

    /**
     * Get country ID
     *
     * @return string|null
     */
    public function getCountryId(): ?string
    {
        return $this->getData('country_id');
    }

    /**
     * Set country ID
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId(string $countryId): PincodeInterface
    {
        return $this->setData('country_id', $countryId);
    }

    /**
     * Get area name
     *
     * @return string|null
     */
    public function getAreaName(): ?string
    {
        return $this->getData('area_name');
    }

    /**
     * Set area name
     *
     * @param string|null $areaName
     * @return $this
     */
    public function setAreaName(?string $areaName): PincodeInterface
    {
        return $this->setData('area_name', $areaName);
    }

    /**
     * Get latitude
     *
     * @return string|null
     */
    public function getLatitude(): ?string
    {
        return $this->getData('latitude');
    }

    /**
     * Set latitude
     *
     * @param string|null $latitude
     * @return $this
     */
    public function setLatitude(?string $latitude): PincodeInterface
    {
        return $this->setData('latitude', $latitude);
    }

    /**
     * Get longitude
     *
     * @return string|null
     */
    public function getLongitude(): ?string
    {
        return $this->getData('longitude');
    }

    /**
     * Set longitude
     *
     * @param string|null $longitude
     * @return $this
     */
    public function setLongitude(?string $longitude): PincodeInterface
    {
        return $this->setData('longitude', $longitude);
    }

    /**
     * Get is deliverable
     *
     * @return bool
     */
    public function getIsDeliverable(): bool
    {
        return (bool) $this->getData('is_deliverable');
    }

    /**
     * Set is deliverable
     *
     * @param bool $isDeliverable
     * @return $this
     */
    public function setIsDeliverable(bool $isDeliverable): PincodeInterface
    {
        return $this->setData('is_deliverable', $isDeliverable);
    }

    /**
     * Get is COD available
     *
     * @return bool
     */
    public function getIsCodAvailable(): bool
    {
        return (bool) $this->getData('is_cod_available');
    }

    /**
     * Set is COD available
     *
     * @param bool $isCodAvailable
     * @return $this
     */
    public function setIsCodAvailable(bool $isCodAvailable): PincodeInterface
    {
        return $this->setData('is_cod_available', $isCodAvailable);
    }

    /**
     * Get estimated delivery days
     *
     * @return int|null
     */
    public function getEstimatedDeliveryDays(): ?int
    {
        return $this->getData('estimated_delivery_days') !== null
            ? (int) $this->getData('estimated_delivery_days')
            : null;
    }

    /**
     * Set estimated delivery days
     *
     * @param int|null $days
     * @return $this
     */
    public function setEstimatedDeliveryDays(?int $days): PincodeInterface
    {
        return $this->setData('estimated_delivery_days', $days);
    }

    /**
     * Get shipping charge
     *
     * @return float|null
     */
    public function getShippingCharge(): ?float
    {
        return $this->getData('shipping_charge') !== null
            ? (float) $this->getData('shipping_charge')
            : null;
    }

    /**
     * Set shipping charge
     *
     * @param float|null $charge
     * @return $this
     */
    public function setShippingCharge(?float $charge): PincodeInterface
    {
        return $this->setData('shipping_charge', $charge);
    }

    /**
     * Get COD charge
     *
     * @return float|null
     */
    public function getCodCharge(): ?float
    {
        return $this->getData('cod_charge') !== null
            ? (float) $this->getData('cod_charge')
            : null;
    }

    /**
     * Set COD charge
     *
     * @param float|null $charge
     * @return $this
     */
    public function setCodCharge(?float $charge): PincodeInterface
    {
        return $this->setData('cod_charge', $charge);
    }

    /**
     * Get weight from
     *
     * @return float|null
     */
    public function getWeightFrom(): ?float
    {
        return $this->getData('weight_from') !== null
            ? (float) $this->getData('weight_from')
            : null;
    }

    /**
     * Set weight from
     *
     * @param float|null $weight
     * @return $this
     */
    public function setWeightFrom(?float $weight): PincodeInterface
    {
        return $this->setData('weight_from', $weight);
    }

    /**
     * Get weight to
     *
     * @return float|null
     */
    public function getWeightTo(): ?float
    {
        return $this->getData('weight_to') !== null
            ? (float) $this->getData('weight_to')
            : null;
    }

    /**
     * Set weight to
     *
     * @param float|null $weight
     * @return $this
     */
    public function setWeightTo(?float $weight): PincodeInterface
    {
        return $this->setData('weight_to', $weight);
    }

    /**
     * Get price from
     *
     * @return float|null
     */
    public function getPriceFrom(): ?float
    {
        return $this->getData('price_from') !== null
            ? (float) $this->getData('price_from')
            : null;
    }

    /**
     * Set price from
     *
     * @param float|null $price
     * @return $this
     */
    public function setPriceFrom(?float $price): PincodeInterface
    {
        return $this->setData('price_from', $price);
    }

    /**
     * Get price to
     *
     * @return float|null
     */
    public function getPriceTo(): ?float
    {
        return $this->getData('price_to') !== null
            ? (float) $this->getData('price_to')
            : null;
    }

    /**
     * Set price to
     *
     * @param float|null $price
     * @return $this
     */
    public function setPriceTo(?float $price): PincodeInterface
    {
        return $this->setData('price_to', $price);
    }

    /**
     * Get store ID
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData('store_id') !== null
            ? (int) $this->getData('store_id')
            : null;
    }

    /**
     * Set store ID
     *
     * @param int|null $storeId
     * @return $this
     */
    public function setStoreId(?int $storeId): PincodeInterface
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * Get customer group ID
     *
     * @return int|null
     */
    public function getCustomerGroupId(): ?int
    {
        return $this->getData('customer_group_id') !== null
            ? (int) $this->getData('customer_group_id')
            : null;
    }

    /**
     * Set customer group ID
     *
     * @param int|null $groupId
     * @return $this
     */
    public function setCustomerGroupId(?int $groupId): PincodeInterface
    {
        return $this->setData('customer_group_id', $groupId);
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority(): int
    {
        return (int) $this->getData('priority') ?: 0;
    }

    /**
     * Set priority
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority): PincodeInterface
    {
        return $this->setData('priority', $priority);
    }
}

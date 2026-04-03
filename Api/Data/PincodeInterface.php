<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api\Data;

interface PincodeInterface
{
    public const ENTITY_ID = 'entity_id';
    public const PINCODE = 'pincode';
    public const COUNTRY_ID = 'country_id';
    public const CITY = 'city';
    public const STATE = 'state';
    public const AREA_NAME = 'area_name';
    public const LATITUDE = 'latitude';
    public const LONGITUDE = 'longitude';
    public const IS_DELIVERABLE = 'is_deliverable';
    public const IS_COD_AVAILABLE = 'is_cod_available';
    public const ESTIMATED_DELIVERY_DAYS = 'estimated_delivery_days';
    public const SHIPPING_CHARGE = 'shipping_charge';
    public const COD_CHARGE = 'cod_charge';
    public const WEIGHT_FROM = 'weight_from';
    public const WEIGHT_TO = 'weight_to';
    public const PRICE_FROM = 'price_from';
    public const PRICE_TO = 'price_to';
    public const STORE_ID = 'store_id';
    public const CUSTOMER_GROUP_ID = 'customer_group_id';
    public const PRIORITY = 'priority';
    public const IS_ACTIVE = 'is_active';

    public function getEntityId();

    public function getPincode(): ?string;

    public function setPincode(string $pincode): PincodeInterface;

    public function getCountryId(): ?string;

    public function setCountryId(string $countryId): PincodeInterface;

    public function getCity(): ?string;

    public function setCity(?string $city): PincodeInterface;

    public function getState(): ?string;

    public function setState(?string $state): PincodeInterface;

    public function getAreaName(): ?string;

    public function setAreaName(?string $areaName): PincodeInterface;

    public function getLatitude(): ?string;

    public function setLatitude(?string $latitude): PincodeInterface;

    public function getLongitude(): ?string;

    public function setLongitude(?string $longitude): PincodeInterface;

    public function getIsDeliverable(): bool;

    public function setIsDeliverable(bool $isDeliverable): PincodeInterface;

    public function getIsCodAvailable(): bool;

    public function setIsCodAvailable(bool $isCodAvailable): PincodeInterface;

    public function getEstimatedDeliveryDays(): ?int;

    public function setEstimatedDeliveryDays(?int $days): PincodeInterface;

    public function getShippingCharge(): ?float;

    public function setShippingCharge(?float $charge): PincodeInterface;

    public function getCodCharge(): ?float;

    public function setCodCharge(?float $charge): PincodeInterface;

    public function getWeightFrom(): ?float;

    public function setWeightFrom(?float $weight): PincodeInterface;

    public function getWeightTo(): ?float;

    public function setWeightTo(?float $weight): PincodeInterface;

    public function getPriceFrom(): ?float;

    public function setPriceFrom(?float $price): PincodeInterface;

    public function getPriceTo(): ?float;

    public function setPriceTo(?float $price): PincodeInterface;

    public function getStoreId(): ?int;

    public function setStoreId(?int $storeId): PincodeInterface;

    public function getCustomerGroupId(): ?int;

    public function setCustomerGroupId(?int $groupId): PincodeInterface;

    public function getPriority(): int;

    public function setPriority(int $priority): PincodeInterface;

    public function getIsActive(): bool;

    public function setIsActive(bool $isActive): PincodeInterface;

    public function getCategories(): array;

    public function setCategories(array $categories): PincodeInterface;

    public function getProducts(): array;

    public function setProducts(array $products): PincodeInterface;
}
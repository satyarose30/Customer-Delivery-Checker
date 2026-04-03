<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\Data\PincodeCheckResultInterface;

class PincodeCheckResult implements PincodeCheckResultInterface
{
    private bool $isAvailable = false;
    private bool $isCodAvailable = false;
    private ?int $estimatedDeliveryDays = null;
    private ?float $shippingCharge = null;
    private ?float $codCharge = null;
    private ?string $city = null;
    private ?string $state = null;
    private ?string $areaName = null;
    private string $message = '';
    private ?int $ruleId = null;
    private array $availableDeliveryDates = [];
    private bool $categoryRestricted = false;
    private bool $productRestricted = false;
    private bool $weightRestricted = false;
    private bool $priceRestricted = false;
    private ?float $currentCartWeight = null;
    private ?float $currentCartValue = null;
    private ?string $latitude = null;
    private ?string $longitude = null;
    private ?string $warranty = null;
    private ?string $returnable = null;

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $available): PincodeCheckResultInterface
    {
        $this->isAvailable = $available;
        return $this;
    }

    public function isCodAvailable(): bool
    {
        return $this->isCodAvailable;
    }

    public function setIsCodAvailable(bool $codAvailable): PincodeCheckResultInterface
    {
        $this->isCodAvailable = $codAvailable;
        return $this;
    }

    public function getEstimatedDeliveryDays(): ?int
    {
        return $this->estimatedDeliveryDays;
    }

    public function setEstimatedDeliveryDays(?int $days): PincodeCheckResultInterface
    {
        $this->estimatedDeliveryDays = $days;
        return $this;
    }

    public function getShippingCharge(): ?float
    {
        return $this->shippingCharge;
    }

    public function setShippingCharge(?float $charge): PincodeCheckResultInterface
    {
        $this->shippingCharge = $charge;
        return $this;
    }

    public function getCodCharge(): ?float
    {
        return $this->codCharge;
    }

    public function setCodCharge(?float $charge): PincodeCheckResultInterface
    {
        $this->codCharge = $charge;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): PincodeCheckResultInterface
    {
        $this->city = $city;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): PincodeCheckResultInterface
    {
        $this->state = $state;
        return $this;
    }

    public function getAreaName(): ?string
    {
        return $this->areaName;
    }

    public function setAreaName(?string $areaName): PincodeCheckResultInterface
    {
        $this->areaName = $areaName;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): PincodeCheckResultInterface
    {
        $this->message = $message;
        return $this;
    }

    public function getRuleId(): ?int
    {
        return $this->ruleId;
    }

    public function setRuleId(?int $ruleId): PincodeCheckResultInterface
    {
        $this->ruleId = $ruleId;
        return $this;
    }

    public function getAvailableDeliveryDates(): array
    {
        return $this->availableDeliveryDates;
    }

    public function setAvailableDeliveryDates(array $dates): PincodeCheckResultInterface
    {
        $this->availableDeliveryDates = $dates;
        return $this;
    }

    public function getCategoryRestricted(): bool
    {
        return $this->categoryRestricted;
    }

    public function setCategoryRestricted(bool $restricted): PincodeCheckResultInterface
    {
        $this->categoryRestricted = $restricted;
        return $this;
    }

    public function getProductRestricted(): bool
    {
        return $this->productRestricted;
    }

    public function setProductRestricted(bool $restricted): PincodeCheckResultInterface
    {
        $this->productRestricted = $restricted;
        return $this;
    }

    public function getWeightRestricted(): bool
    {
        return $this->weightRestricted;
    }

    public function setWeightRestricted(bool $restricted): PincodeCheckResultInterface
    {
        $this->weightRestricted = $restricted;
        return $this;
    }

    public function getPriceRestricted(): bool
    {
        return $this->priceRestricted;
    }

    public function setPriceRestricted(bool $restricted): PincodeCheckResultInterface
    {
        $this->priceRestricted = $restricted;
        return $this;
    }

    public function getCurrentCartWeight(): ?float
    {
        return $this->currentCartWeight;
    }

    public function setCurrentCartWeight(?float $weight): PincodeCheckResultInterface
    {
        $this->currentCartWeight = $weight;
        return $this;
    }

    public function getCurrentCartValue(): ?float
    {
        return $this->currentCartValue;
    }

    public function setCurrentCartValue(?float $value): PincodeCheckResultInterface
    {
        $this->currentCartValue = $value;
        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): PincodeCheckResultInterface
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): PincodeCheckResultInterface
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getWarranty(): ?string
    {
        return $this->warranty;
    }

    public function setWarranty(?string $warranty): PincodeCheckResultInterface
    {
        $this->warranty = $warranty;
        return $this;
    }

    public function getReturnable(): ?string
    {
        return $this->returnable;
    }

    public function setReturnable(?string $returnable): PincodeCheckResultInterface
    {
        $this->returnable = $returnable;
        return $this;
    }

    public function __toArray(): array
    {
        return [
            'is_available' => $this->isAvailable,
            'is_cod_available' => $this->isCodAvailable,
            'estimated_delivery_days' => $this->estimatedDeliveryDays,
            'shipping_charge' => $this->shippingCharge,
            'cod_charge' => $this->codCharge,
            'city' => $this->city,
            'state' => $this->state,
            'area_name' => $this->areaName,
            'message' => $this->message,
            'rule_id' => $this->ruleId,
            'available_delivery_dates' => $this->availableDeliveryDates,
            'category_restricted' => $this->categoryRestricted,
            'product_restricted' => $this->productRestricted,
            'weight_restricted' => $this->weightRestricted,
            'price_restricted' => $this->priceRestricted,
            'current_cart_weight' => $this->currentCartWeight,
            'current_cart_value' => $this->currentCartValue,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'warranty' => $this->warranty,
            'returnable' => $this->returnable,
        ];
    }

    public function getData(): array
    {
        return $this->__toArray();
    }
}
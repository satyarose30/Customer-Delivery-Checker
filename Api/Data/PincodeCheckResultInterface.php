<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api\Data;

interface PincodeCheckResultInterface
{
    public function isAvailable(): bool;

    public function setIsAvailable(bool $available): PincodeCheckResultInterface;

    public function isCodAvailable(): bool;

    public function setIsCodAvailable(bool $codAvailable): PincodeCheckResultInterface;

    public function getEstimatedDeliveryDays(): ?int;

    public function setEstimatedDeliveryDays(?int $days): PincodeCheckResultInterface;

    public function getShippingCharge(): ?float;

    public function setShippingCharge(?float $charge): PincodeCheckResultInterface;

    public function getCodCharge(): ?float;

    public function setCodCharge(?float $charge): PincodeCheckResultInterface;

    public function getCity(): ?string;

    public function setCity(?string $city): PincodeCheckResultInterface;

    public function getState(): ?string;

    public function setState(?string $state): PincodeCheckResultInterface;

    public function getAreaName(): ?string;

    public function setAreaName(?string $areaName): PincodeCheckResultInterface;

    public function getMessage(): string;

    public function setMessage(string $message): PincodeCheckResultInterface;

    public function getRuleId(): ?int;

    public function setRuleId(?int $ruleId): PincodeCheckResultInterface;

    public function getAvailableDeliveryDates(): array;

    public function setAvailableDeliveryDates(array $dates): PincodeCheckResultInterface;

    public function getCategoryRestricted(): bool;

    public function setCategoryRestricted(bool $restricted): PincodeCheckResultInterface;

    public function getProductRestricted(): bool;

    public function setProductRestricted(bool $restricted): PincodeCheckResultInterface;

    public function getWeightRestricted(): bool;

    public function setWeightRestricted(bool $restricted): PincodeCheckResultInterface;

    public function getPriceRestricted(): bool;

    public function setPriceRestricted(bool $restricted): PincodeCheckResultInterface;

    public function getCurrentCartWeight(): ?float;

    public function setCurrentCartWeight(?float $weight): PincodeCheckResultInterface;

    public function getCurrentCartValue(): ?float;

    public function setCurrentCartValue(?float $value): PincodeCheckResultInterface;

    /**
     * @return string|null
     */
    public function getWarranty(): ?string;

    /**
     * @param string|null $warranty
     * @return $this
     */
    public function setWarranty(?string $warranty): PincodeCheckResultInterface;

    /**
     * @return string|null
     */
    public function getReturnable(): ?string;

    /**
     * @param string|null $returnable
     * @return $this
     */
    public function setReturnable(?string $returnable): PincodeCheckResultInterface;
}
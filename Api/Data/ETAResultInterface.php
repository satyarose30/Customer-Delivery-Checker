<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Api\Data;
/**
 * ETA Result Interface
 * @api
 */

interface ETAResultInterface
{
    /**
     * Get estimated delivery date
     *
     * @return string|null
     */
     public function getEstimatedDeliveryDate(): ?string;

    /**
     * Set estimated delivery date
     *
     * @param string $date
     * @return $this
     */
    public function setEstimatedDeliveryDate(string $date): self;

    /**
     * Get delivery days
     *
     * @return int|null
     */
    public function getDeliveryDays(): ?int;

    /**
     * Set delivery days
     *
     * @param int $days
     * @return $this
     */
    public function setDeliveryDays(int $days): self;
}

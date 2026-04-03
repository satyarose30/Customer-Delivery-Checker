<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model;

use Domus\CustomerDeliveryChecker\Api\Data\ExpressResultInterface;

class ExpressResult implements ExpressResultInterface
{
    private bool $isEligible = false;
    private ?float $additionalCost = null;

    /**
     * @return bool
     */
    public function getIsEligible()
    {
        return $this->isEligible;
    }

    /**
     * @param bool $isEligible
     * @return $this
     */
    public function setIsEligible($isEligible)
    {
        $this->isEligible = (bool)$isEligible;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAdditionalCost()
    {
        return $this->additionalCost;
    }

    /**
     * @param float $cost
     * @return $this
     */
    public function setAdditionalCost($cost)
    {
        $this->additionalCost = (float)$cost;
        return $this;
    }
}

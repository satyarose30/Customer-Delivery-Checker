<?php
namespace Domus\CustomerDeliveryChecker\Api\Data;

interface ExpressResultInterface
{
    /**
     * @return bool
     */
    public function getIsEligible();

    /**
     * @param bool $isEligible
     * @return $this
     */
    public function setIsEligible($isEligible);

    /**
     * @return float|null
     */
    public function getAdditionalCost();

    /**
     * @param float $cost
     * @return $this
     */
    public function setAdditionalCost($cost);
}

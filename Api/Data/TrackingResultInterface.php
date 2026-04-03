<?php
namespace Domus\CustomerDeliveryChecker\Api\Data;

interface TrackingResultInterface
{
    /**
     * @return string|null
     */
    public function getStatus();

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getDetails();

    /**
     * @param string $details
     * @return $this
     */
    public function setDetails($details);
}

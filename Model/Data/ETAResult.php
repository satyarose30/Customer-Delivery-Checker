<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use Domus\CustomerDeliveryChecker\Api\Data\ETAResultInterface;

class ETAResult extends AbstractSimpleObject implements ETAResultInterface
{
    private const ESTIMATED_DELIVERY_DATE = 'estimated_delivery_date';
    private const DELIVERY_DAYS = 'delivery_days';

    /**
     * @inheritdoc
     */
    public function getEstimatedDeliveryDate(): ?string
    {
        return $this->_get(self::ESTIMATED_DELIVERY_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setEstimatedDeliveryDate(string $date): ETAResultInterface
    {
        return $this->setData(self::ESTIMATED_DELIVERY_DATE, $date);
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryDays(): ?int
    {
        return $this->_get(self::DELIVERY_DAYS);
    }

    /**
     * @inheritdoc
     */
    public function setDeliveryDays(int $days): ETAResultInterface
    {
        return $this->setData(self::DELIVERY_DAYS, $days);
    }
}
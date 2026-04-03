<?php
// app/code/Domus/CustomerDeliveryChecker/Model/Cache/PincodeCache.php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Cache;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

/**
 * Class PincodeCache
 * @package Domus\CustomerDeliveryChecker\Model\Cache
 */
class PincodeCache extends TagScope
{
    /**
     * Cache type code
     */
    const TYPE_IDENTIFIER = 'domus_pincode_cache';
    
    /**
     * Cache tag
     */
    const CACHE_TAG = 'DOMUS_PINCODE';

    /**
     * PincodeCache constructor.
     *
     * @param FrontendPool $cacheFrontendPool
     */
    public function __construct(FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}

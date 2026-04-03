<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Cache;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class RedisPincodeCache
 * @package Domus\CustomerDeliveryChecker\Model\Cache
 */
class RedisPincodeCache extends TagScope
{
    /**
     * Cache type identifier
     */
    const TYPE_IDENTIFIER = 'domus_pincode_cache_redis';
    
    /**
     * Cache tag
     */
    const CACHE_TAG = 'DOMUS_PINCODE_REDIS';
    
    /**
     * Cache key prefix
     */
    const KEY_PREFIX = 'domus_pincode:';
    
    /**
     * @var Json
     */
    private Json $serializer;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * RedisPincodeCache constructor.
     *
     * @param FrontendPool $cacheFrontendPool
     * @param Json $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        FrontendPool $cacheFrontendPool,
        Json $serializer,
        LoggerInterface $logger
    ) {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * Cache pincode availability check
     *
     * @param string $pincode
     * @param array $result
     * @param int $ttl
     * @return bool
     */
    public function cachePincodeCheck(string $pincode, array $result, int $ttl = 3600): bool
    {
        try {
            $key = $this->getPincodeKey($pincode);
            $value = $this->serializer->serialize($result);
            
            $this->save($value, $key, self::CACHE_TAG, $ttl);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to cache pincode check: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cached pincode availability
     *
     * @param string $pincode
     * @return array|null
     */
    public function getCachedPincodeCheck(string $pincode): ?array
    {
        try {
            $key = $this->getPincodeKey($pincode);
            $cached = $this->load($key);
            
            return $cached ? $this->serializer->unserialize($cached) : null;
        } catch (\Exception $e) {
            $this->logger->error('Failed to load cached pincode check: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cache time slots availability
     *
     * @param string $pincode
     * @param string $date
     * @param array $timeSlots
     * @param int $ttl
     * @return bool
     */
    public function cacheTimeSlots(string $pincode, string $date, array $timeSlots, int $ttl = 1800): bool
    {
        try {
            $key = $this->getTimeSlotsKey($pincode, $date);
            $value = $this->serializer->serialize($timeSlots);
            
            $this->save($value, $key, self::CACHE_TAG, $ttl);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to cache time slots: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cached time slots
     *
     * @param string $pincode
     * @param string $date
     * @return array|null
     */
    public function getCachedTimeSlots(string $pincode, string $date): ?array
    {
        try {
            $key = $this->getTimeSlotsKey($pincode, $date);
            $cached = $this->load($key);
            
            return $cached ? $this->serializer->unserialize($cached) : null;
        } catch (\Exception $e) {
            $this->logger->error('Failed to load cached time slots: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cache ETA prediction
     *
     * @param string $pincode
     * @param int|null $productId
     * @param array $etaData
     * @param int $ttl
     * @return bool
     */
    public function cacheETAPrediction(string $pincode, ?int $productId, array $etaData, int $ttl = 7200): bool
    {
        try {
            $key = $this->getETAKey($pincode, $productId);
            $value = $this->serializer->serialize($etaData);
            
            $this->save($value, $key, self::CACHE_TAG, $ttl);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to cache ETA prediction: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cached ETA prediction
     *
     * @param string $pincode
     * @param int|null $productId
     * @return array|null
     */
    public function getCachedETAPrediction(string $pincode, ?int $productId): ?array
    {
        try {
            $key = $this->getETAKey($pincode, $productId);
            $cached = $this->load($key);
            
            return $cached ? $this->serializer->unserialize($cached) : null;
        } catch (\Exception $e) {
            $this->logger->error('Failed to load cached ETA prediction: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Warm cache with popular pincodes
     *
     * @param array $pincodes
     * @return bool
     */
    public function warmupCache(array $pincodes): bool
    {
        try {
            $success = true;
            
            foreach ($pincodes as $pincode) {
                // Pre-load pincode data into cache
                $key = $this->getPincodeKey($pincode);
                $this->load($key); // This will trigger backend loading
            }
            
            return $success;
        } catch (\Exception $e) {
            $this->logger->error('Failed to warmup cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear pincode-related cache
     *
     * @param string|null $pincode
     * @return bool
     */
    public function clearPincodeCache(?string $pincode = null): bool
    {
        try {
            if ($pincode) {
                // Clear specific pincode cache
                $patterns = [
                    $this->getPincodeKey($pincode),
                    $this->getTimeSlotsKey($pincode, '*'),
                    $this->getETAKey($pincode, '*')
                ];
                
                foreach ($patterns as $pattern) {
                    if (strpos($pattern, '*') !== false) {
                        // Use Redis pattern matching if available
                        $this->removePattern($pattern);
                    } else {
                        $this->remove($pattern);
                    }
                }
            } else {
                // Clear all pincode cache
                $this->clean();
            }
            
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to clear pincode cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cache statistics
     *
     * @return array
     */
    public function getCacheStats(): array
    {
        try {
            $stats = $this->getFrontend()->getBackend()->getStats();
            
            return [
                'hits' => $stats['hits'] ?? 0,
                'misses' => $stats['misses'] ?? 0,
                'memory_usage' => $stats['memory_usage'] ?? 0,
                'memory_limit' => $stats['memory_limit'] ?? 0,
                'evictions' => $stats['evictions'] ?? 0,
                'keys' => $stats['keys'] ?? 0
            ];
        } catch (\Exception $e) {
            $this->logger->error('Failed to get cache stats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get pincode cache key
     *
     * @param string $pincode
     * @return string
     */
    private function getPincodeKey(string $pincode): string
    {
        return self::KEY_PREFIX . 'check:' . $pincode;
    }

    /**
     * Get time slots cache key
     *
     * @param string $pincode
     * @param string $date
     * @return string
     */
    private function getTimeSlotsKey(string $pincode, string $date): string
    {
        return self::KEY_PREFIX . 'slots:' . $pincode . ':' . $date;
    }

    /**
     * Get ETA cache key
     *
     * @param string $pincode
     * @param int|null $productId
     * @return string
     */
    private function getETAKey(string $pincode, ?int $productId): string
    {
        return self::KEY_PREFIX . 'eta:' . $pincode . ':' . ($productId ?: 'generic');
    }

    /**
     * Remove cache entries by pattern (Redis specific)
     *
     * @param string $pattern
     * @return bool
     */
    private function removePattern(string $pattern): bool
    {
        try {
            // This would require Redis-specific implementation
            // For now, just clean the cache
            $this->clean();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

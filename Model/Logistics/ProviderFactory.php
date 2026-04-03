<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Logistics;

use Domus\CustomerDeliveryChecker\Api\LogisticsProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class ProviderFactory
{
    private const XML_PATH_ACTIVE_PROVIDER = 'customer_delivery_checker/general/logistics_provider';
    private const DEFAULT_PROVIDER = 'manual';

    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly array $providers = []
    ) {
    }

    /**
     * Create the active logistics provider instance
     *
     * @return LogisticsProviderInterface
     */
    public function create(): LogisticsProviderInterface
    {
        $activeCode = $this->scopeConfig->getValue(
            self::XML_PATH_ACTIVE_PROVIDER,
            ScopeInterface::SCOPE_STORE
        ) ?: self::DEFAULT_PROVIDER;

        if (!isset($this->providers[$activeCode])) {
            $activeCode = self::DEFAULT_PROVIDER;
        }

        $className = $this->providers[$activeCode] ?? ManualProvider::class;
        
        return $this->objectManager->create($className);
    }

    /**
     * Get available providers
     *
     * @return array
     */
    public function getAvailableProviders(): array
    {
        return $this->providers;
    }
}

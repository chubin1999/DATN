<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Provider;

use Magento\Store\Model\ScopeInterface;

class GeneralConfiguration
{
    private const XML_PATH_IS_ENABLED = 'brand_reward/general/enabled';
    private const XML_PATH_LIFE_TIME = 'brand_reward/general/life_time';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getLifetime(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_LIFE_TIME, ScopeInterface::SCOPE_STORE);
    }
}

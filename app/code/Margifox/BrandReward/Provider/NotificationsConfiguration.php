<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Provider;

use Magento\Store\Model\ScopeInterface;

class NotificationsConfiguration
{
    const XML_PATH_NOTIFICATION_IS_ENABLED = 'brand_reward/notification/is_enabled';
    const XML_PATH_NOTIFICATION_SENDER = 'brand_reward/notification/sender';
    const XML_PATH_NOTIFICATION_EXPIRED_NOTIFICATION_TEMPLATE = 'brand_reward/notification/expired_notification_template';
    const XML_PATH_NOTIFICATION_EXPIRE_DAYS_BEFORE = 'brand_reward/notification/expiry_day_before';

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
    public function isNotificationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NOTIFICATION_IS_ENABLED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getNotificationSender(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFICATION_SENDER,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getExpiredNotificationTemplate(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFICATION_EXPIRED_NOTIFICATION_TEMPLATE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return int
     */
    public function getExpiryDayBefore(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFICATION_EXPIRE_DAYS_BEFORE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}

<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Cron;

use Margifox\BrandReward\Model\Email\Notification;
use Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistory\CollectionFactory;
use Margifox\BrandReward\Provider\GeneralConfiguration;
use Margifox\BrandReward\Provider\NotificationsConfiguration;

class ExpirePromoAllocationNotification
{
    /**
     * @var GeneralConfiguration
     */
    protected $generalConfiguration;

    /**
     * @var NotificationsConfiguration
     */
    protected $notificationsConfiguration;

    /**
     * @var CollectionFactory
     */
    protected $spendingHistoryFactory;

    /**
     * @var \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistoryFactory
     */
    protected $companyBrandSpendingHistoryFactory;

    /**
     * @var Notification
     */
    protected $notification;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param GeneralConfiguration $generalConfiguration
     * @param NotificationsConfiguration $notificationsConfiguration
     * @param CollectionFactory $spendingHistoryFactory
     * @param Notification $notification
     * @param \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistoryFactory $companyBrandSpendingHistoryFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        GeneralConfiguration $generalConfiguration,
        NotificationsConfiguration $notificationsConfiguration,
        CollectionFactory $spendingHistoryFactory,
        Notification $notification,
        \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistoryFactory $companyBrandSpendingHistoryFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->generalConfiguration = $generalConfiguration;
        $this->notificationsConfiguration = $notificationsConfiguration;
        $this->spendingHistoryFactory = $spendingHistoryFactory;
        $this->companyBrandSpendingHistoryFactory = $companyBrandSpendingHistoryFactory;
        $this->notification = $notification;
        $this->logger = $logger;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function execute()
    {
        if (!$this->notificationsConfiguration->isNotificationEnabled()) {
            return $this;
        }

        $expiryInDays = $this->notificationsConfiguration->getExpiryDayBefore();
        $lifetime = $this->generalConfiguration->getLifetime();
        $diffDays = $lifetime - $expiryInDays;
        if (!$lifetime || $diffDays <= 0) {
            return $this;
        }

        /** @var \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistory\Collection $spendingHistoryCollection */
        $spendingHistoryCollection = $this->spendingHistoryFactory->create();
        $spendingHistoryCollection->loadExpiredSoonPoints($diffDays);

        foreach ($spendingHistoryCollection as $item) {
            try {
                $this->notification->sendPointsExpiredNotification($item);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        $historyIds = $spendingHistoryCollection->getExpiredSoonIds();
        $this->companyBrandSpendingHistoryFactory->create()->markAsNotified($historyIds);

        return $this;
    }
}

<?php

namespace Margifox\BrandReward\Cron;

class ScheduledPointsExpiration
{
    /**
     * @var \Margifox\BrandReward\Provider\GeneralConfiguration
     */
    protected $generalConfiguration;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistoryFactory
     */
    protected $companyBrandSpendingHistory;

    /**
     * @param \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfiguration
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistoryFactory $companyBrandSpendingHistory
     */
    public function __construct(
        \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfiguration,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistoryFactory $companyBrandSpendingHistory
    ) {
        $this->generalConfiguration = $generalConfiguration;
        $this->storeManager = $storeManager;
        $this->companyBrandSpendingHistory = $companyBrandSpendingHistory;
    }

    public function execute()
    {
        if (!$this->generalConfiguration->isEnabled()) {
            return;
        }

        $expiryDate = $this->generalConfiguration->getLifetime();
        $this->companyBrandSpendingHistory->create()->expirePoints($expiryDate, 100);

        // TODO: update company level
    }
}

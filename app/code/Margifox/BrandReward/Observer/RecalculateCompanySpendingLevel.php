<?php

namespace Margifox\BrandReward\Observer;

use Magento\Framework\Event\ObserverInterface;
use Margifox\BrandReward\Model\Source\SpendingHistory\ActionType;
use Margifox\BrandReward\Model\Source\SpendingHistory\RewardType;

class RecalculateCompanySpendingLevel implements ObserverInterface
{
    /**
     * @var \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface
     */
    private $companyBrandSpendingLevelRepository;

    /**
     * @param \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository
     */
    public function __construct(
        \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository
    ) {
        $this->companyBrandSpendingLevelRepository = $companyBrandSpendingLevelRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $companyId = $observer->getData('companyId');
        /** @var \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface $brandSpendingHistory */
        $brandSpendingHistory = $observer->getData('history');

        $spendingLevel = $this->companyBrandSpendingLevelRepository->getByCompanyAndBrand($companyId, $brandSpendingHistory->getBrandId());
        if ($spendingLevel === null) {
            return $this;
        }

        if ($brandSpendingHistory->getActionType() == ActionType::REDEEM) {
            if ($brandSpendingHistory->getRewardType() == RewardType::LOYALTY) {
                $loyaltyBalance = $spendingLevel->getLoyaltyPointsBalance();
                $loyaltyBalance -= $brandSpendingHistory->getPointsDelta();
                $spendingLevel->setLoyaltyPointsBalance($loyaltyBalance);
            }

            if ($brandSpendingHistory->getRewardType() == RewardType::PROMO_ALLOCATION) {
                $allocationAmount = $spendingLevel->getAllocationPointsBalance();
                $allocationAmount -= $brandSpendingHistory->getPointsDelta();
                $spendingLevel->setAllocationPointsBalance($allocationAmount);
            }
        }

        if ($brandSpendingHistory->getLoyaltyPointsEarned()) {
            $loyaltyBalance = $spendingLevel->getLoyaltyPointsBalance();
            $loyaltyBalance += $brandSpendingHistory->getLoyaltyPointsEarned();
            $spendingLevel->setLoyaltyPointsBalance($loyaltyBalance);
        }

        if ($brandSpendingHistory->getRewardsAllocationAmount()) {
            $allocationAmount = $spendingLevel->getAllocationPointsBalance();
            $allocationAmount += $brandSpendingHistory->getRewardsAllocationAmount();
            $spendingLevel->setAllocationPointsBalance($allocationAmount);
        }

        // TODO: convert balance to points

        $this->companyBrandSpendingLevelRepository->save($spendingLevel);

        return $this;
    }
}

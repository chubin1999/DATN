<?php

namespace Margifox\BrandReward\Model;

use Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface;

class CompanyBrandSpendingHistory extends \Magento\Framework\Model\AbstractModel implements CompanyBrandSpendingHistoryInterface
{
    /*public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }*/

    protected function _construct()
    {
        $this->_init(ResourceModel\CompanyBrandSpendingHistory::class);
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function beforeSave()
    {
        if ($this->getPointsDelta() < 0) {
            $this->spendAvailableLoyaltyPoints($this->getPointsDelta());
        }

        return parent::beforeSave();
    }

    public function getRewardType()
    {
        return $this->getData(self::REWARD_TYPE);
    }

    public function setRewardType($rewardType)
    {
        return $this->setData(self::REWARD_TYPE, $rewardType);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getCompanyId()
    {
        return $this->getData(self::COMPANY_ID);
    }

    public function setCompanyId(int $companyId)
    {
        return $this->setData(self::COMPANY_ID, $companyId);
    }

    public function getBrandId()
    {
        return $this->getData(self::BRAND_ID);
    }

    public function setBrandId(int $brandId)
    {
        return $this->setData(self::BRAND_ID, $brandId);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getOrigTransactionAmount()
    {
        return $this->getData(self::ORIG_TRANSACTION_AMOUNT);
    }

    public function setOrigTransactionAmount($origTransactionAmount)
    {
        return $this->setData(self::ORIG_TRANSACTION_AMOUNT, $origTransactionAmount);
    }

    public function getLoyaltyPointsEarned()
    {
        return $this->getData(self::LOYALTY_POINTS_EARNED);
    }

    public function setLoyaltyPointsEarned($loyaltyPointsEarned)
    {
        return $this->setData(self::LOYALTY_POINTS_EARNED, $loyaltyPointsEarned);
    }

    public function getRewardsAllocationAmount()
    {
        return $this->getData(self::REWARDS_ALLOCATION_AMOUNT);
    }

    public function setRewardsAllocationAmount($rewardsAllocationAmount)
    {
        return $this->setData(self::REWARDS_ALLOCATION_AMOUNT, $rewardsAllocationAmount);
    }

    public function getActionType()
    {
        return $this->getData(self::ACTION_TYPE);
    }

    public function setActionType($actionType)
    {
        return $this->setData(self::ACTION_TYPE, $actionType);
    }

    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    public function setOrderId(?int $orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getCreditMemoId()
    {
        return $this->getData(self::CREDIT_MEMO_ID);
    }

    public function setCreditMemoId($creditMemoId)
    {
        return $this->setData(self::CREDIT_MEMO_ID, $creditMemoId);
    }

    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    public function getPointsDelta()
    {
        return $this->getData(self::POINTS_DELTA);
    }

    public function setPointsDelta($pointsDelta)
    {
        return $this->setData(self::POINTS_DELTA, $pointsDelta);
    }

    public function getPointsUsed()
    {
        return $this->getData(self::POINTS_USED);
    }

    public function setPointsUsed($pointsUsed)
    {
        return $this->setData(self::POINTS_USED, $pointsUsed);
    }

    public function getNewBalancePoints()
    {
        return $this->getData(self::NEW_BALANCE_POINTS);
    }

    public function setNewBalancePoints($newBalancePoints)
    {
        return $this->setData(self::NEW_BALANCE_POINTS, $newBalancePoints);
    }

    public function getNewBalancePromoAllocation()
    {
        return $this->getData(self::NEW_BALANCE_PROMO_ALLOCATIONS);
    }

    public function setNewBalancePromoAllocation($newBalancePromoAllocation)
    {
        return $this->setData(self::NEW_BALANCE_PROMO_ALLOCATIONS, $newBalancePromoAllocation);
    }

    public function getExpiryNotificationSent()
    {
        return $this->getData(self::EXPIRY_NOTIFICATION_SENT);
    }

    public function setExpiryNotificationSent($expiryNotificationSent)
    {
        return $this->setData(self::EXPIRY_NOTIFICATION_SENT, $expiryNotificationSent);
    }

    /**
     * Spend unused points for required amount
     *
     * @param int $required Points total that required
     * @return $this
     */
    protected function spendAvailableLoyaltyPoints($required)
    {
        return $this->getResource()->useAvailableLoyaltyPoints($this, $required);
    }
}

<?php

namespace Margifox\BrandReward\Api\Data;

interface CompanyBrandSpendingHistoryInterface
{
    const HISTORY_ID = 'history_id';
    const REWARD_TYPE = 'reward_type';
    const STATUS = 'status';
    const COMPANY_ID = 'company_id';
    const BRAND_ID = 'brand_id';
    const CREATED_AT = 'created_at';
    const ORIG_TRANSACTION_AMOUNT = 'origin_transaction_amount';
    const LOYALTY_POINTS_EARNED = 'loyalty_points_earned';
    const REWARDS_ALLOCATION_AMOUNT = 'rewards_allocation_amount';
    const ACTION_TYPE = 'action_type';
    const ORDER_ID = 'order_id';
    const CREDIT_MEMO_ID = 'credit_memo_id';

    const INCREMENT_ID = 'increment_id';
    const POINTS_DELTA = 'points_delta';
    const POINTS_USED = 'points_used';

    const NEW_BALANCE_POINTS = 'new_balance_points';
    const NEW_BALANCE_PROMO_ALLOCATIONS = 'new_balance_promo_allocation';
    const EXPIRY_NOTIFICATION_SENT = 'expiry_notification_sent';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id);

    /**
     * @return string
     */
    public function getRewardType();

    /**
     * @param string $rewardType
     * @return self
     */
    public function setRewardType($rewardType);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     * @return self
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getCompanyId();

    /**
     * @param int $companyId
     * @return self
     */
    public function setCompanyId(int $companyId);

    /**
     * @return int
     */
    public function getBrandId();

    /**
     * @param int $brandId
     * @return self
     */
    public function setBrandId(int $brandId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt);

    /**
     * @return float
     */
    public function getOrigTransactionAmount();

    /**
     * @param float $origTransactionAmount
     * @return self
     */
    public function setOrigTransactionAmount($origTransactionAmount);

    /**
     * @return float
     */
    public function getLoyaltyPointsEarned();

    /**
     * @param float $loyaltyPointsEarned
     * @return self
     */
    public function setLoyaltyPointsEarned($loyaltyPointsEarned);

    /**
     * @return float
     */
    public function getRewardsAllocationAmount();

    /**
     * @param float $rewardsAllocationAmount
     * @return self
     */
    public function setRewardsAllocationAmount($rewardsAllocationAmount);

    /**
     * @return string
     */
    public function getActionType();

    /**
     * @param string $actionType
     * @return self
     */
    public function setActionType($actionType);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int|null $orderId
     * @return self
     */
    public function setOrderId(?int $orderId);

    /**
     * @return string
     */
    public function getIncrementId();

    /**
     * @param string $incrementId
     * @return self
     */
    public function setIncrementId($incrementId);

    /**
     * @return int
     */
    public function getCreditMemoId();

    /**
     * @param int $creditMemoId
     * @return self
     */
    public function setCreditMemoId($creditMemoId);

    /**
     * @return int
     */
    public function getPointsDelta();

    /**
     * @param int $pointsDelta
     * @return self
     */
    public function setPointsDelta($pointsDelta);

    /**
     * @return int
     */
    public function getPointsUsed();

    /**
     * @param int $pointsUsed
     * @return self
     */
    public function setPointsUsed($pointsUsed);

    /**
     * @return float
     */
    public function getNewBalancePoints();

    /**
     * @param float $newBalancePoints
     * @return self
     */
    public function setNewBalancePoints($newBalancePoints);

    /**
     * @return float
     */
    public function getNewBalancePromoAllocation();

    /**
     * @param float $newBalancePromoAllocation
     * @return self
     */
    public function setNewBalancePromoAllocation($newBalancePromoAllocation);

    /**
     * @return bool
     */
    public function getExpiryNotificationSent();

    /**
     * @param bool $expiryNotificationSent
     * @return self
     */
    public function setExpiryNotificationSent($expiryNotificationSent);
}

<?php

namespace Margifox\BrandReward\Api\Data;

interface CompanyBrandSpendingLevelInterface
{
    const ID = 'id';
    const COMPANY_ID = 'company_id';
    const BRAND_ID = 'brand_id';
    const SPENDING_LEVEL = 'spending_level';
    const LOYALTY_POINTS_BALANCE = 'loyalty_points_balance';
    const ALLOCATION_POINTS_BALANCE = 'allocation_points_balance';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getCompanyId();

    /**
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId(int $companyId);

    /**
     * @return int
     */
    public function getBrandId();

    /**
     * @param int $brandId
     * @return $this
     */
    public function setBrandId(int $brandId);

    /**
     * @return string
     */
    public function getSpendingLevel();

    /**
     * @param string $spendingLevel
     * @return $this
     */
    public function setSpendingLevel($spendingLevel);

    /**
     * @return int
     */
    public function getLoyaltyPointsBalance();

    /**
     * @param int $loyaltyPointsBalance
     * @return $this
     */
    public function setLoyaltyPointsBalance($loyaltyPointsBalance = 0);

    /**
     * @return int
     */
    public function getAllocationPointsBalance();

    /**
     * @param int $allocationPointsBalance
     * @return $this
     */
    public function setAllocationPointsBalance($allocationPointsBalance = 0);

    /**
     * TODO: or move it to some converter (or delegate)
     * @param float $amount
     * @return float
     */
    public function getPointsEquivalent($amount);
}

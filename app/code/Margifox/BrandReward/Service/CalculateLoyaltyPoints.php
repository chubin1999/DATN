<?php

namespace Margifox\BrandReward\Service;

use Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface;
use Margifox\BrandReward\Provider\SpendingLevelProvider;

class CalculateLoyaltyPoints
{
    /**
     * @var \Margifox\BrandReward\Provider\SpendingLevelProvider
     */
    private $spendingLevelProvider;

    /**
     * @param SpendingLevelProvider $spendingLevelProvider
     */
    public function __construct(
        SpendingLevelProvider $spendingLevelProvider
    ) {
        $this->spendingLevelProvider = $spendingLevelProvider;
    }

    /**
     * @param float $price
     * @param CompanyBrandSpendingLevelInterface $companySpendingLevel
     * @return float
     */
    public function execute($price, CompanyBrandSpendingLevelInterface $companySpendingLevel)
    {
        $spendingLevel = $this->spendingLevelProvider->getPointsEarnedPerDollar($companySpendingLevel->getBrandId());
        if (isset($spendingLevel[$companySpendingLevel->getSpendingLevel()])) {
            $pointsPerDollar = $spendingLevel[$companySpendingLevel->getSpendingLevel()];
        }
        $reward = ($pointsPerDollar ?? 0) * $price;

        return intval($reward);
    }
}

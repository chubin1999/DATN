<?php

namespace Margifox\BrandReward\Service;

use Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface;
use Margifox\BrandReward\Provider\SpendingLevelProvider;

class CalculatePromoAllocation
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
        $spendingLevel = $this->spendingLevelProvider->getPromoAllocation($companySpendingLevel->getBrandId());
        if (isset($spendingLevel[$companySpendingLevel->getSpendingLevel()])) {
            $promoAllocationPercent = $spendingLevel[$companySpendingLevel->getSpendingLevel()];
            $promoAllocationPercent /= 100;
        }
        $reward = ($promoAllocationPercent ?? 0) * $price;

        return round($reward, 2);
    }
}

<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Provider;

class SpendingLevelProvider
{
    const XML_PATH_POINTS_EARNED_PER_DOLLAR = 'brand_reward/spending_level/points_earned_per';
    const XML_PATH_POINTS_TO_REDEEM = 'brand_reward/spending_level/points_to_redeem';
    const XML_PATH_MIN_SALES_PER_YEAR = 'brand_reward/spending_level/min_sales_per_year';
    const XML_PATH_MIN_SALES_PER_MONTH = 'brand_reward/spending_level/min_sales_per_month';
    const XML_PATH_PROMO_ALLOCATION = 'brand_reward/spending_level/promotional_allocation';

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
     * @param int $brandId
     * @return array
     */
    public function getPointsEarnedPerDollar($brandId)
    {
        return $this->convertToArrayOfSpendingLevels(
            $this->scopeConfig->getValue(self::XML_PATH_POINTS_EARNED_PER_DOLLAR),
            $brandId
        );
    }

    /**
     * @param int $brandId
     * @return array
     */
    public function getPointsToRedeem($brandId)
    {
        return $this->convertToArrayOfSpendingLevels(
            $this->scopeConfig->getValue(self::XML_PATH_POINTS_TO_REDEEM),
            $brandId
        );
    }

    /**
     * @param int $brandId
     * @return array
     */
    public function getMinSalesPerYear($brandId)
    {
        return $this->convertToArrayOfSpendingLevels(
            $this->scopeConfig->getValue(self::XML_PATH_MIN_SALES_PER_YEAR),
            $brandId
        );
    }

    /**
     * @param int $brandId
     * @return array
     */
    public function getMinSalesPerMonth($brandId)
    {
        return $this->convertToArrayOfSpendingLevels(
            $this->scopeConfig->getValue(self::XML_PATH_MIN_SALES_PER_MONTH),
            $brandId
        );
    }

    /**
     * @param int $brandId
     * @return array
     */
    public function getPromoAllocation($brandId)
    {
        return $this->convertToArrayOfSpendingLevels(
            $this->scopeConfig->getValue(self::XML_PATH_PROMO_ALLOCATION),
            $brandId
        );
    }

    /**
     * @param string $value
     * @param int $brandId
     * @return array
     */
    private function convertToArrayOfSpendingLevels($value, $brandId)
    {
        $toArray = json_decode($value, true);
        foreach ($toArray as $item) {
            if ($item['brand'] == $brandId) {
                unset($item['brand']);
                asort($item);

                return $item;
            }
        }

        return [];
    }
}

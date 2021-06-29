<?php

namespace Margifox\BrandReward\Model;

use Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface;

class CompanyBrandSpendingLevel extends \Magento\Framework\Model\AbstractModel implements CompanyBrandSpendingLevelInterface
{
    /**
     * @var \Magento\Reward\Model\Reward\RateFactory
     */
    protected $rateFactory;

    /**
     * @var array
     */
    protected $rates;

    public function __construct(
        \Magento\Reward\Model\Reward\RateFactory $rateFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->rateFactory = $rateFactory;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\CompanyBrandSpendingLevel::class);
    }

    public function getPointsEquivalent($amount)
    {
        $points = 0;
        if (!$amount) {
            return $points;
        }

        $ratePointsCount = $this->getRateToCurrency()->getPoints();
        $rateCurrencyAmount = $this->getRateToCurrency()->getCurrencyAmount();
        if ($rateCurrencyAmount > 0) {
            $delta = $amount / $rateCurrencyAmount;
            if ($delta > 0) {
                $points = $ratePointsCount * ceil($delta);
            }
        }

        return $points;
    }

    /**
     * Return rate to convert points to currency amount
     *
     * @return \Magento\Reward\Model\Reward\Rate
     * @codeCoverageIgnore
     */
    public function getRateToCurrency()
    {
        return $this->getRateByDirection(\Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY);
    }

    /**
     * @return int
     */
    public function getCompanyId()
    {
        return $this->getData(self::COMPANY_ID);
    }

    /**
     * @param int $companyId
     * @return CompanyBrandSpendingLevelInterface|CompanyBrandSpendingLevel
     */
    public function setCompanyId(int $companyId)
    {
        return $this->setData(self::COMPANY_ID, $companyId);
    }

    /**
     * @return int
     */
    public function getBrandId()
    {
        return $this->getData(self::BRAND_ID);
    }

    /**
     * @param int $brandId
     * @return CompanyBrandSpendingLevelInterface|CompanyBrandSpendingLevel
     */
    public function setBrandId(int $brandId)
    {
        return $this->setData(self::BRAND_ID, $brandId);
    }

    /**
     * @return string
     */
    public function getSpendingLevel()
    {
        return $this->getData(self::SPENDING_LEVEL);
    }

    /**
     * @param string $spendingLevel
     * @return CompanyBrandSpendingLevel
     */
    public function setSpendingLevel($spendingLevel)
    {
        return $this->setData(self::SPENDING_LEVEL, $spendingLevel);
    }

    /**
     * @return int
     */
    public function getLoyaltyPointsBalance()
    {
        return $this->getData(self::LOYALTY_POINTS_BALANCE);
    }

    /**
     * @param int $loyaltyPointsBalance
     * @return CompanyBrandSpendingLevelInterface|CompanyBrandSpendingLevel
     */
    public function setLoyaltyPointsBalance($loyaltyPointsBalance = 0)
    {
        return $this->setData(self::LOYALTY_POINTS_BALANCE, $loyaltyPointsBalance);
    }

    /**
     * @return int
     */
    public function getAllocationPointsBalance()
    {
        return $this->getData(self::ALLOCATION_POINTS_BALANCE);
    }

    /**
     * @param int $allocationPointsBalance
     * @return CompanyBrandSpendingLevelInterface|CompanyBrandSpendingLevel
     */
    public function setAllocationPointsBalance($allocationPointsBalance = 0)
    {
        return $this->setData(self::ALLOCATION_POINTS_BALANCE, $allocationPointsBalance);
    }

    protected function getRateByDirection($direction)
    {
        // TODO; customerGroupId and websiteId
        if (!isset($this->rates[$direction])) {
            $this->rates[$direction] = $this->rateFactory->create()->fetch(
                1,
                1,
                $direction
            );
        }
        return $this->rates[$direction];
    }
}

<?php

namespace Margifox\BrandReward\Model\DTO;

use Margifox\Brand\Api\Data\BrandInterface;
use Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface;

class QuotePricePerBrand
{
    /**
     * @var BrandInterface
     */
    private $brand;

    /**
     * @var CompanyBrandSpendingLevelInterface
     */
    private $spendingLevel;

    /**
     * @var float
     */
    private $quoteBrandPrice;

    /**
     * @var float
     */
    private $quoteBrandPriceForPromo;

    /**
     * @var float
     */
    private $quoteBrandPriceForFullPrice;

    /**
     * @var \Magento\Quote\Model\Quote\Item[]
     */
    private $quoteFullItems;

    /**
     * @var \Magento\Quote\Model\Quote\Item[]
     */
    private $quotePromoItems;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @return BrandInterface
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param BrandInterface $brand
     * @return $this
     */
    public function setBrand(BrandInterface $brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return CompanyBrandSpendingLevelInterface
     */
    public function getSpendingLevel()
    {
        return $this->spendingLevel;
    }

    /**
     * @param CompanyBrandSpendingLevelInterface $spendingLevel
     * @return $this
     */
    public function setSpendingLevel(CompanyBrandSpendingLevelInterface $spendingLevel)
    {
        $this->spendingLevel = $spendingLevel;
        return $this;
    }

    /**
     * @return float
     */
    public function getQuoteBrandPrice()
    {
        return $this->quoteBrandPrice;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setQuoteBrandPrice($price)
    {
        $this->quoteBrandPrice = $price;
        return $this;
    }

    /**
     * @return float
     */
    public function getQuoteBrandPriceForPromo()
    {
        return $this->quoteBrandPriceForPromo;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setQuoteBrandPriceForPromo($price)
    {
        $this->quoteBrandPriceForPromo = $price;
        return $this;
    }

    /**
     * @return float
     */
    public function getQuoteBrandPriceForFullPrice()
    {
        return $this->quoteBrandPriceForFullPrice;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setQuoteBrandPriceForFullPrice($price)
    {
        $this->quoteBrandPriceForFullPrice = $price;
        return $this;
    }

    /**
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function getQuoteFullItems()
    {
        return $this->quoteFullItems;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item[] $items
     * @return $this
     */
    public function setQuoteFullItems($items)
    {
        $this->quoteFullItems = $items;
        return $this;
    }

    /**
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function getQuotePromoItems()
    {
        return $this->quotePromoItems;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item[] $items
     * @return $this
     */
    public function setQuotePromoItems($items)
    {
        $this->quotePromoItems = $items;
        return $this;
    }

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActiveRewardsForBrand($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActiveRewardsForBrand()
    {
        return $this->isActive;
    }
}

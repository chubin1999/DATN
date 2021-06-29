<?php

namespace Margifox\BrandReward\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address;

class Reward extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Margifox\BrandReward\Provider\GeneralConfiguration
     */
    protected $generalConfiguration;

    /**
     * @var \Magento\Reward\Helper\Data
     */
    protected $rewardData = null;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Margifox\BrandReward\Service\GetCurrentCustomerCompany
     */
    protected $currentCustomerCompany;

    /**
     * @var \Margifox\BrandReward\Service\GetQuoteItemsPerBrands
     */
    protected $quoteItemsPerBrands;

    /**
     * @param \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfiguration
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Margifox\BrandReward\Service\GetCurrentCustomerCompany $currentCustomerCompany
     * @param \Margifox\BrandReward\Service\GetQuoteItemsPerBrands $quoteItemsPerBrands
     */
    public function __construct(
        \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfiguration,
        \Magento\Reward\Helper\Data $rewardData,
        PriceCurrencyInterface $priceCurrency,
        \Margifox\BrandReward\Service\GetCurrentCustomerCompany $currentCustomerCompany,
        \Margifox\BrandReward\Service\GetQuoteItemsPerBrands $quoteItemsPerBrands
    ) {
        $this->generalConfiguration = $generalConfiguration;
        $this->priceCurrency = $priceCurrency;
        $this->rewardData = $rewardData;
        $this->setCode('reward');
        $this->currentCustomerCompany = $currentCustomerCompany;
        $this->quoteItemsPerBrands = $quoteItemsPerBrands;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param Address\Total $total
     * @return $this|Address\Total\AbstractTotal
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        if (!$this->generalConfiguration->isEnabled()) {
            return $this;
        }

        $total->setRewardPointsBalance(0)
            ->setRewardCurrencyAmount(0)
            ->setBaseRewardCurrencyAmount(0);

        if ($total->getBaseGrandTotal() > 0 && $quote->getCustomer()->getId() && $quote->getUseRewardPoints()) {
            $brandRewardIds = $quote->getData('brand_reward_ids');
            $brandRewardIds = $brandRewardIds ? unserialize($brandRewardIds) : [];
            if (!$brandRewardIds) {
                return $this;
            }

            $quoteBrandPrices = $this->quoteItemsPerBrands->execute($quote);
            $pointsTotalToBeUsed = 0;
            $currencyTotalToBeUsed = 0;
            $baseCurrencyTotalToBeUsed = 0;

            foreach ($quoteBrandPrices as $quoteBrandPrice) {
                if (!$quoteBrandPrice->getIsActiveRewardsForBrand()) {
                    continue;
                }
                $pricePerBrand = 0;
                $spendingLevel = $quoteBrandPrice->getSpendingLevel();
                $availablePoints = $spendingLevel->getLoyaltyPointsBalance();
                $availableRewardDollars = $spendingLevel->getLoyaltyPointsBalance() / 1;
                if (isset($brandRewardIds['promo_allocation']) && in_array($quoteBrandPrice->getBrand()->getId(), $brandRewardIds['promo_allocation'])) {
                    $pricePerBrand += $quoteBrandPrice->getQuoteBrandPriceForPromo();// TODO: convert points to dollars
                }
                if (isset($brandRewardIds['loyalty']) && in_array($quoteBrandPrice->getBrand()->getId(), $brandRewardIds['loyalty'])) {
                    $pricePerBrand += $quoteBrandPrice->getQuoteBrandPriceForFullPrice();// TODO: convert points to dollars
                }

                $pointsLeft = $availablePoints /*- $quote->getRewardPointsBalance()*/;  // TODO: It may be wrong for different brands
                $baseRewardCurrencyAmountLeft = $availableRewardDollars /*- $quote->getBaseRewardCurrencyAmount()*/;

                $rewardCurrencyAmountLeft = $this->priceCurrency->convert($availablePoints, $quote->getStore()) /*- $quote->getRewardCurrencyAmount()*/;
                if ($baseRewardCurrencyAmountLeft > $pricePerBrand) {
                    $сurrencyAmountToBeUsed = $pricePerBrand * 1; // TODO: convert to points?
                    $baseCurrencyAmountToBeUsed = $pricePerBrand;
                } else {
                    $сurrencyAmountToBeUsed = $rewardCurrencyAmountLeft;
                    $baseCurrencyAmountToBeUsed = $baseRewardCurrencyAmountLeft;
                }

                // TODO: skip billing address
                /*if ($baseRewardCurrencyAmountLeft >= $total->getBaseGrandTotal()) {
                    $total->setGrandTotal(0);
                    $total->setBaseGrandTotal(0);
                    continue;
                }*/

                $pointsEquivalentToBeUsed = $spendingLevel->getPointsEquivalent($baseRewardCurrencyAmountLeft);
                if ($pointsEquivalentToBeUsed > $pointsLeft) {
                    $pointsEquivalentToBeUsed = $pointsLeft;
                }
                if ($pointsEquivalentToBeUsed > $pricePerBrand) {
                    $pointsEquivalentToBeUsed = $pricePerBrand;
                }

                $pointsTotalToBeUsed += $pointsEquivalentToBeUsed;
                $currencyTotalToBeUsed += $сurrencyAmountToBeUsed;
                $baseCurrencyTotalToBeUsed += $baseCurrencyAmountToBeUsed;
            }

            if ($total->getGrandTotal() > 0) {
                $total->setGrandTotal($total->getGrandTotal() - $currencyTotalToBeUsed);
                $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseCurrencyTotalToBeUsed);
            }

            $quote->setRewardPointsBalance(round($quote->getRewardPointsBalance() + $pointsTotalToBeUsed));
            $quote->setRewardCurrencyAmount($quote->getRewardCurrencyAmount() + $currencyTotalToBeUsed);
            $quote->setBaseRewardCurrencyAmount($quote->getBaseRewardCurrencyAmount() + $baseCurrencyTotalToBeUsed);

            $total->setRewardPointsBalance(round($pointsTotalToBeUsed));
            $total->setRewardCurrencyAmount($currencyTotalToBeUsed);
            $total->setBaseRewardCurrencyAmount($baseCurrencyTotalToBeUsed);
        }

        return $this;
    }

    /**
     * Retrieve reward total data and set it to quote address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address|Address\Total $total
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if (!$this->rewardData->isEnabledOnFront()) {
            return null;
        }
        if ($total->getRewardCurrencyAmount()) {
            return [
                'code' => $this->getCode(),
                'title' => $this->rewardData->formatReward($total->getRewardPointsBalance()),
                'value' => -$total->getRewardCurrencyAmount(),
            ];
        }

        return null;
    }
}

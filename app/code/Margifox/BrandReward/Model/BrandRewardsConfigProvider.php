<?php

namespace Margifox\BrandReward\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\UrlInterface;

class BrandRewardsConfigProvider implements ConfigProviderInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Margifox\BrandReward\Service\GetCurrentCustomerCompany
     */
    private $getCurrentCustomerCompany;

    /**
     * @var \Margifox\BrandReward\Service\GetQuoteItemsPerBrands
     */
    private $quoteItemsPerBrands;

    /**
     * @param CheckoutSession $checkoutSession
     * @param UrlInterface $urlBuilder
     * @param \Margifox\BrandReward\Service\GetCurrentCustomerCompany $getCurrentCustomerCompany
     * @param \Margifox\BrandReward\Service\GetQuoteItemsPerBrands $quoteItemsPerBrands
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        UrlInterface $urlBuilder,
        \Margifox\BrandReward\Service\GetCurrentCustomerCompany $getCurrentCustomerCompany,
        \Margifox\BrandReward\Service\GetQuoteItemsPerBrands $quoteItemsPerBrands
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->urlBuilder = $urlBuilder;
        $this->getCurrentCustomerCompany = $getCurrentCustomerCompany;
        $this->quoteItemsPerBrands = $quoteItemsPerBrands;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
        $quote = $this->checkoutSession->getQuote();
        if (!$this->getCurrentCustomerCompany->execute()) {
            return $this->emptyData();
        }
        $quotePricePerBrand = $this->quoteItemsPerBrands->execute($quote);
        if ($quotePricePerBrand === null) {
            return $this->emptyData();
        }

        $frontendBrandLoyalty = [];
        $frontendBrandPromoAllocation = [];
        foreach ($quotePricePerBrand as $brandInfo) {
            $element = [
                'id' => $brandInfo->getBrand()->getId(),
                'label' => $brandInfo->getBrand()->getName(),
                'isChecked' => $brandInfo->getIsActiveRewardsForBrand()
            ];
            if ($brandInfo->getSpendingLevel()->getLoyaltyPointsBalance() > 0 && $brandInfo->getQuoteBrandPriceForFullPrice() > 0) {
                // TODO: convert points balance to dollars
                $element['available_amount'] = __(
                    '$%1 Available',
                    $brandInfo->getSpendingLevel()->getLoyaltyPointsBalance()
                );
                $element['useText'] = __(
                    'Use rewards from %1 ($%2)',
                    $brandInfo->getBrand()->getName(),
                    $brandInfo->getQuoteBrandPriceForFullPrice()
                );
                $frontendBrandLoyalty[] = $element;
            }

            if ($brandInfo->getSpendingLevel()->getAllocationPointsBalance() > 0 && $brandInfo->getQuoteBrandPriceForPromo() > 0) {
                // TODO: convert points balance to dollars
                $element['available_amount'] = __(
                    '$%1 Available',
                    $brandInfo->getSpendingLevel()->getAllocationPointsBalance()
                );
                $element['useText'] = __(
                    'Use rewards from %1 ($%2)',
                    $brandInfo->getBrand()->getName(),
                    $brandInfo->getQuoteBrandPriceForPromo()
                );
                $frontendBrandPromoAllocation[] = $element;
            }
        }

        $config = [
            'payment' => [
                'rewards' => [
                    'isAvailable' => (bool)$frontendBrandLoyalty,
                    'amountSubstracted' => (bool)$quote->getUseRewardPoints(),
                    'usedAmount' => (float)$quote->getBaseRewardCurrencyAmount(),
                    'brands' => $frontendBrandLoyalty
                ],
                'promo_allocations' => [
                    'isAvailable' => (bool)$frontendBrandPromoAllocation,
                    'amountSubstracted' => (bool)$quote->getUseRewardPoints(),
                    'usedAmount' => (float)$quote->getBaseRewardCurrencyAmount(),
                    'brands' => $frontendBrandPromoAllocation
                ]
            ],
            'review' => [
                'brand_reward' => [
                    'removeUrl' => $this->urlBuilder->getUrl('brand_reward/cart/remove')
                ],
            ],
        ];

        return $config;
    }

    private function emptyData()
    {
        return [
            'payment' => [
                'rewards' => [
                    'isAvailable' => (bool)false,
                    'amountSubstracted' => (bool)false,
                    'usedAmount' => 0,
                    'brands' => []
                ],
                'promo_allocations' => [
                    'isAvailable' => (bool)false,
                    'amountSubstracted' => (bool)false,
                    'usedAmount' =>  0,
                    'brands' => []
                ]
            ],
            'review' => [
                'brand_reward' => [
                    'removeUrl' => $this->urlBuilder->getUrl('brand_reward/cart/remove')
                ],
            ],
        ];
    }
}

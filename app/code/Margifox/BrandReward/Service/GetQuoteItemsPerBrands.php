<?php

namespace Margifox\BrandReward\Service;

class GetQuoteItemsPerBrands
{
    /**
     * @var \Margifox\Brand\Model\Brand\Repository
     */
    protected $brandRepository;

    /**
     * @var \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface
     */
    protected $companyBrandSpendingLevel;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var \Margifox\BrandReward\Model\DTO\QuotePricePerBrandFactory
     */
    protected $quotePricePerBrandFactory;

    /**
     * @var \Magento\Reward\Model\ResourceModel\Reward\Rate\CollectionFactory
     */
    protected $rateCollectionFactory;

    /**
     * @param \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevel
     * @param \Margifox\Brand\Model\Brand\Repository $brandRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param \Margifox\BrandReward\Model\DTO\QuotePricePerBrandFactory $quotePricePerBrandFactory
     */
    public function __construct(
        \Margifox\Brand\Model\Brand\Repository $brandRepository,
        \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevel,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Margifox\BrandReward\Model\DTO\QuotePricePerBrandFactory $quotePricePerBrandFactory,
        \Magento\Reward\Model\ResourceModel\Reward\Rate\CollectionFactory $rateCollectionFactory
    ) {
        $this->brandRepository = $brandRepository;
        $this->companyBrandSpendingLevel = $companyBrandSpendingLevel;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->quotePricePerBrandFactory = $quotePricePerBrandFactory;
        $this->rateCollectionFactory = $rateCollectionFactory;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Margifox\BrandReward\Model\DTO\QuotePricePerBrand[]
     */
    public function execute($quote)
    {
        $brands = $this->brandRepository->getAll();
        $quoteBrandIds = [];
        foreach ($brands as $brand) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($quote->getAllVisibleItems() as $item) {
                $product = $item->getProduct();
                if ($product->getData('brand') == $brand->getOptionLinkId()) {
                    $quoteBrandIds[] = $brand->getId();
                }
            }
        }
        if (!$quoteBrandIds) {
            return [];
        }

        $productBrands = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productBrands[$product->getData('brand')][] = $item;
        }

        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria->addFilter('brand_id', $quoteBrandIds, 'in');

        $companySpendingLevels = $this->companyBrandSpendingLevel->getList($searchCriteria->create())->getItems();
        if (!$companySpendingLevels) {
            return null;
        }
        // TODO: simplify it with one query
        foreach ($companySpendingLevels as $key => $companySpendingLevel) {
            /*$rateCollection = $this->rateCollectionFactory->create();
            $brandHasRate = $rateCollection->addFieldToFilter('customer_group_id', 0)
                ->addFieldToFilter('direction', \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY)
                ->addFieldToFilter('brand_id', $companySpendingLevel->getBrandId())
                ->getSize();

            if (!$brandHasRate) {
                unset($companySpendingLevels[$key]);
            }*/
        }

        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria->addFilter(
            'entity_id',
            $quoteBrandIds,
            'in'
        );

        $brands = $this->brandRepository->getList($searchCriteria->create())->getItems();
        $quotePricePerBrand = [];

        $brandRewardIds = $quote->getData('brand_reward_ids');
        $brandRewardIds = $brandRewardIds ? unserialize($brandRewardIds) : [];
        $brandRewardIds = call_user_func_array('array_merge', $brandRewardIds);
        $brandRewardIds = array_values($brandRewardIds);

        foreach ($productBrands as $brandId => $items) {
            $brandPrice = 0;
            foreach ($brands as $brand) {
                if ($brandId == $brand->getOptionLinkId()) {
                    foreach ($companySpendingLevels as $companySpendingLevel) {
                        if ($companySpendingLevel->getBrandId() == $brand->getId()) {
                            $quoteItemsFull = [];
                            $quoteItemsPromo = [];
                            $brandPromoPrice = 0;
                            $brandFullProductPrice = 0;
                            /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
                            foreach ($items as $quoteItem) {
                                $brandPrice += $quoteItem->getRowTotalInclTax();

                                // TODO: identify is product promo or not. Hardcoded value
                                if ($quoteItem->getProduct()->getData('product_type') == 36) {
                                    $brandPromoPrice += $quoteItem->getRowTotalInclTax();
                                    $quoteItemsPromo[] = $quoteItem;
                                } else {
                                    $brandFullProductPrice += $quoteItem->getRowTotalInclTax();
                                    $quoteItemsFull[] = $quoteItem;
                                }
                            }

                            $quotePerBrand = $this->quotePricePerBrandFactory->create();
                            $quotePerBrand->setSpendingLevel($companySpendingLevel);
                            $quotePerBrand->setBrand($brand);
                            $quotePerBrand->setQuoteBrandPrice($brandPrice);
                            $quotePerBrand->setQuoteBrandPriceForPromo($brandPromoPrice);
                            $quotePerBrand->setQuoteBrandPriceForFullPrice($brandFullProductPrice);
                            $quotePerBrand->setQuoteFullItems($quoteItemsFull);
                            $quotePerBrand->setQuotePromoItems($quoteItemsPromo);
                            $quotePerBrand->setIsActiveRewardsForBrand(
                                $brandRewardIds ? in_array($brand->getId(), $brandRewardIds) : false
                            );

                            $quotePricePerBrand[] = $quotePerBrand;
                        }
                    }
                }
            }

        }

        return $quotePricePerBrand;
    }
}

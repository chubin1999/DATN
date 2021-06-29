<?php

namespace Margifox\BrandReward\Service;

class GetOrderItemsPerAvailableCompanyBrand
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
     * @var GetCurrentCustomerCompany
     */
    protected $currentCustomerCompany;

    /**
     * @param \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevel
     * @param \Margifox\Brand\Model\Brand\Repository $brandRepository
     * @param GetCurrentCustomerCompany $currentCustomerCompany
     */
    public function __construct(
        \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevel,
        \Margifox\Brand\Model\Brand\Repository $brandRepository,
        \Margifox\BrandReward\Service\GetCurrentCustomerCompany $currentCustomerCompany
    ) {
        $this->brandRepository = $brandRepository;
        $this->companyBrandSpendingLevel = $companyBrandSpendingLevel;
        $this->currentCustomerCompany = $currentCustomerCompany;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute($order)
    {
        $companyId = $this->currentCustomerCompany->execute();
        if (!$companyId) {
            return [];
        }

        $productBrands = [];
        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productBrands[$product->getData('brand')][] = $item;
        }

        // Get Spending levels for company
        $companySpendingLevels = $this->companyBrandSpendingLevel->getByCompany($companyId);
        if (!$companySpendingLevels) {
            return [];
        }

        $brands = $this->brandRepository->getAll();
        $infoPerBrand = [];
        foreach ($brands as $brand) {
            // Link brand->id and brandOption->id
            foreach ($productBrands as $brandId => $items) {
                if ($brandId == $brand->getOptionLinkId()) {
                    foreach ($items as $orderItem) {
                        $infoPerBrand[$brand->getId()][] = $orderItem;
                    }
                }
            }
        }

        return $infoPerBrand;
    }
}

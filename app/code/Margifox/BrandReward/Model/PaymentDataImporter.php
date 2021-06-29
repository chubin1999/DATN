<?php

namespace Margifox\BrandReward\Model;

class PaymentDataImporter
{
    /**
     * @var \Margifox\BrandReward\Service\GetCurrentCustomerCompany
     */
    protected $currentCustomerCompany;

    /**
     * @var \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface
     */
    protected $companyBrandSpendingLevelRepository;

    public function __construct(
        \Margifox\BrandReward\Service\GetCurrentCustomerCompany $currentCustomerCompany,
        \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository
    ) {
        $this->currentCustomerCompany = $currentCustomerCompany;
        $this->companyBrandSpendingLevelRepository = $companyBrandSpendingLevelRepository;
    }

    /**
     * Prepare and set to quote Company Spending Level
     * set zero subtotal checkout payment if need
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Framework\DataObject $payment
     * @param int $brandId
     * @param bool $useRewardPoints
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function import($quote, $payment, $brandId, $useRewardPoints)
    {
        if (!$quote ||
            !$quote->getCustomerId() ||
            $quote->getBaseGrandTotal() + $quote->getBaseRewardCurrencyAmount() <= 0
        ) {
            return $this;
        }

        $quote->setUseRewardPoints((bool)$useRewardPoints);
        if (!$quote->getUseRewardPoints()) {
            return $this;
        }

        $companyId = $this->currentCustomerCompany->execute();
        if ($companyId === null) {
            $quote->setUseRewardPoints(false);
            return $this;
        }

        $spendingLevel = $this->companyBrandSpendingLevelRepository->getByCompanyAndBrand($companyId, $brandId);
        if (!$spendingLevel) {
            $quote->setUseRewardPoints(false);
            return $this;
        }

        $quote->setSpendingLevelInstance($spendingLevel);
        if (!$payment->getMethod()) {
            $payment->setMethod('free');
        }

        return $this;
    }
}

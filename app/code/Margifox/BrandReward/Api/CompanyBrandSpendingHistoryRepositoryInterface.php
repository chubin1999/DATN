<?php

namespace Margifox\BrandReward\Api;

interface CompanyBrandSpendingHistoryRepositoryInterface
{
    /**
     * @param int $companyId
     * @return Data\CompanyBrandSpendingHistoryInterface[]|[]
     */
    public function getByCompany($companyId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param Data\CompanyBrandSpendingHistoryInterface $companyBrandSpendingHistory
     * @return CompanyBrandSpendingLevelRepositoryInterface
     */
    public function save(Data\CompanyBrandSpendingHistoryInterface $companyBrandSpendingHistory);

    /**
     * @param string $dateInterval
     * @param int $companyId
     * @param int $brandId
     * @return float
     */
    public function getCompanyBrandTotalTransactionAmount(string $dateInterval, int $companyId, int $brandId): float;
}

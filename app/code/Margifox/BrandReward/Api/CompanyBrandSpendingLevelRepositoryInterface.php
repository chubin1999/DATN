<?php

namespace Margifox\BrandReward\Api;

interface CompanyBrandSpendingLevelRepositoryInterface
{
    /**
     * @return \Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelSearchResultsInterface
     */
    public function getAll();

    /**
     * @param int $companyId
     * @return Data\CompanyBrandSpendingLevelInterface[]|[]
     */
    public function getByCompany($companyId);

    /**
     * @param int $companyId
     * @param int $brandId
     * @return Data\CompanyBrandSpendingLevelInterface|null
     */
    public function getByCompanyAndBrand($companyId, $brandId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param Data\CompanyBrandSpendingLevelInterface $companyBrandSpendingLevel
     * @return CompanyBrandSpendingLevelRepositoryInterface
     */
    public function save(Data\CompanyBrandSpendingLevelInterface $companyBrandSpendingLevel);
}

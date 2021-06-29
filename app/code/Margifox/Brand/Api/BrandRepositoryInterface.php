<?php

namespace Margifox\Brand\Api;

interface BrandRepositoryInterface
{
    /**
     * @return \Margifox\Brand\Api\Data\BrandSearchResultsInterface
     */
    public function getAll();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Margifox\Brand\Api\Data\BrandSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $id
     * @return \Margifox\Brand\Api\Data\BrandInterface
     */
    public function getById($id);
}

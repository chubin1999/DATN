<?php

namespace Margifox\BrandReward\Repository;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface;
use Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface;
use Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingLevel\CollectionFactory;

class CompanyBrandSpendingLevel implements CompanyBrandSpendingLevelRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingLevel
     */
    private $resourceModel;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param \Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingLevel $resourceModel
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        \Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelSearchResultsInterfaceFactory $searchResultsFactory,
        \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingLevel $resourceModel,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel = $resourceModel;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @return \Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelSearchResultsInterface
     */
    public function getAll()
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();

        return $this->getList($searchCriteria->create())->getItems();
    }

    /**
     * @param int $companyId
     * @return \Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface[]
     */
    public function getByCompany($companyId)
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria->addFilter('company_id', $companyId, 'in');

        return $this->getList($searchCriteria->create())->getItems();
    }

    /**
     * @param int $companyId
     * @param int $brandId
     * @return \Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface
     */
    public function getByCompanyAndBrand($companyId, $brandId)
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria->addFilter('company_id', $companyId, 'in');
        $searchCriteria->addFilter('brand_id', $brandId, 'in');

        $items = $this->getList($searchCriteria->create())->getItems();

        return $items ? reset($items) : null;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return CompanyBrandSpendingLevelInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    $sortOrder->getDirection()
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @param CompanyBrandSpendingLevelInterface $companyBrandSpendingLevel
     * @return CompanyBrandSpendingLevelInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CompanyBrandSpendingLevelInterface $companyBrandSpendingLevel)
    {
        $this->resourceModel->save($companyBrandSpendingLevel);

        return $companyBrandSpendingLevel;
    }
}

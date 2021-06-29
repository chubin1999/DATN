<?php

namespace Margifox\BrandReward\Repository;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Margifox\BrandReward\Api\CompanyBrandSpendingHistoryRepositoryInterface;
use Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistory\CollectionFactory;

class CompanyBrandSpendingHistory implements CompanyBrandSpendingHistoryRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistory
     */
    private $resourceModel;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterfaceFactory $searchResultsFactory
     * @param \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistory $resourceModel
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterfaceFactory $searchResultsFactory,
        \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistory $resourceModel,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel = $resourceModel;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param int $companyId
     * @return \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface[]
     */
    public function getByCompany($companyId)
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria->addFilter('company_id', $companyId, 'in');

        return $this->getList($searchCriteria->create())->getItems();
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface
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
     * @param \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface $companyBrandSpendingLevel
     * @return \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(\Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface $companyBrandSpendingLevel)
    {
        $this->resourceModel->save($companyBrandSpendingLevel);

        return $companyBrandSpendingLevel;
    }

    /**
     * @param string $dateInterval
     * @param int $companyId
     * @param int $brandId
     * @return float
     * @throws \Exception
     */
    public function getCompanyBrandTotalTransactionAmount(string $dateInterval, int $companyId, int $brandId): float
    {
        $yearAgo = (new \DateTime())->sub((new \DateInterval($dateInterval)));
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('company_id', $companyId)
            ->addFieldToFilter('brand_id', $brandId)
            ->addFieldToFilter('created_at', ['gteq' => $yearAgo->format('Y-m-d')]);

        $collection->getSelect()
            ->columns([
                'total' => new \Zend_Db_Expr('SUM(orig_transaction_amount)')
            ]);

        $value = $collection->getColumnValues('total');

        return reset($value);
    }
}

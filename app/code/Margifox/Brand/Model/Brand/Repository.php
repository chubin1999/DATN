<?php

namespace Margifox\Brand\Model\Brand;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Margifox\Brand\Api\BrandRepositoryInterface;
use Margifox\Brand\Api\Data\BrandSearchResultsInterface;
use Margifox\Brand\Api\Data\BrandSearchResultsInterfaceFactory;
use Margifox\Brand\Model\ResourceModel\Brand\CollectionFactory;

class Repository implements BrandRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var BrandSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var \Margifox\Brand\Model\ResourceModel\Brand
     */
    private $brandResource;

    /**
     * @var \Margifox\Brand\Model\BrandFactory
     */
    private $brandFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param BrandSearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param \Margifox\Brand\Model\ResourceModel\Brand $brandResource
     * @param \Margifox\Brand\Model\BrandFactory $brandFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        BrandSearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Margifox\Brand\Model\ResourceModel\Brand $brandResource,
        \Margifox\Brand\Model\BrandFactory $brandFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->brandResource = $brandResource;
        $this->brandFactory = $brandFactory;
    }

    /**
     * @return \Margifox\Brand\Api\Data\BrandInterface[]
     */
    public function getAll()
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();

        return $this->getList($searchCriteria->create())->getItems();
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return BrandSearchResultsInterface
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
     * @param int $id
     * @return \Margifox\Brand\Api\Data\BrandInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $brand = $this->brandFactory->create();
        $this->brandResource->load($brand, $id);
        if (!$brand->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Unable to find Brand with ID "%1"', $id)
            );
        }

        return $brand;
    }
}

<?php

namespace Margifox\BrandReward\Api\Data;

interface CompanyBrandSpendingLevelSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return CompanyBrandSpendingLevelInterface[]
     */
    public function getItems();

    /**
     * @param CompanyBrandSpendingLevelInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

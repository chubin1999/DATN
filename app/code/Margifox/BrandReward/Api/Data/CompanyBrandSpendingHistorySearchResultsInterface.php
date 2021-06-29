<?php

namespace Margifox\BrandReward\Api\Data;

interface CompanyBrandSpendingHistorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return CompanyBrandSpendingHistoryInterface[]
     */
    public function getItems();

    /**
     * @param CompanyBrandSpendingHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

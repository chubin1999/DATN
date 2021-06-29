<?php

namespace Margifox\Brand\Api\Data;

interface BrandSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return BrandInterface[]
     */
    public function getItems();

    /**
     * @param BrandInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

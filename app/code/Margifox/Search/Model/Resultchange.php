<?php

namespace Margifox\Search\Model;

class Resultchange {
    protected $catalogSearchData;

    public function __construct(
        \Magento\CatalogSearch\Helper\Data $catalogSearchData
    ) {
        $this->catalogSearchData = $catalogSearchData;
    }


    public function aftergetSearchQueryText()
    {
        return __("Search results for '%1'", $this->catalogSearchData->getEscapedQueryText());
    }
    
}


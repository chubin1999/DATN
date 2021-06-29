<?php

namespace Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingLevel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            \Margifox\BrandReward\Model\CompanyBrandSpendingLevel::class,
            \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingLevel::class
        );
    }
}

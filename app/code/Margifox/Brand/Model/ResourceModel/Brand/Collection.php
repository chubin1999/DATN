<?php

namespace Margifox\Brand\Model\ResourceModel\Brand;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(
            \Margifox\Brand\Model\Brand::class,
            \Margifox\Brand\Model\ResourceModel\Brand::class
        );
    }
}

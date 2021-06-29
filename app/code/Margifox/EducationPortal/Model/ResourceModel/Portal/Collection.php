<?php

namespace Margifox\EducationPortal\Model\ResourceModel\Portal;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Margifox\EducationPortal\Model\Portal::class,
            \Margifox\EducationPortal\Model\ResourceModel\Portal::class
        );
    }
}

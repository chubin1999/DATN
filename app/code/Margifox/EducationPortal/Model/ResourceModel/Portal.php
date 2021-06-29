<?php

namespace Margifox\EducationPortal\Model\ResourceModel;

class Portal extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('education_portal', 'id');
    }
}

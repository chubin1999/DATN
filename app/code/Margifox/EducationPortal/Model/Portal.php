<?php

namespace Margifox\EducationPortal\Model;

class Portal extends \Magento\Framework\Model\AbstractModel
{
    /**
     * set the resource model
     */
    protected function _construct()
    {
        $this->_init('Margifox\EducationPortal\Model\ResourceModel\Portal');
    }
}

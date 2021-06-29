<?php

namespace Margifox\BrandReward\Model\ResourceModel;

class CompanyBrandSpendingLevel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('company_brand_spending_level', 'id');
    }

    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getId() && !$object->getData('spending_level')) {
            // TODO: should it be copper or null?
            $object->setData('spending_level', \Margifox\BrandReward\Model\Source\SpendingLevel::COPPER);
        }

        return parent::save($object);
    }
}

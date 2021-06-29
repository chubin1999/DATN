<?php

namespace Margifox\BrandReward\Model\ResourceModel\Reward;

class Rate extends \Magento\Reward\Model\ResourceModel\Reward\Rate
{
    /**
     * @param $websiteId
     * @param $customerGroupId
     * @param $direction
     * @param $brandId
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRateDataForBrand($websiteId, $customerGroupId, $direction, $brandId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('website_id = :website_id')
            ->where('customer_group_id = :customer_group_id')
            ->where('direction = :direction')
            ->where('brand_id = :brand_id');
        $bind = [
            ':website_id' => (int)$websiteId,
            ':customer_group_id' => (int)$customerGroupId,
            ':direction' => $direction,
            ':brand_id' => $brandId
        ];
        $data = $this->getConnection()->fetchRow($select, $bind);
        if ($data) {
            return $data;
        }

        return [];
    }
}

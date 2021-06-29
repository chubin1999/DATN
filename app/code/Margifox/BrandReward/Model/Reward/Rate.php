<?php

namespace Margifox\BrandReward\Model\Reward;

class Rate extends \Magento\Reward\Model\Reward\Rate
{
    /**
     * @param int $websiteId
     * @param int $customerGroupId
     * @param int $direction
     * @param int $brandId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIsRateUniqueForBrandToCurrent($websiteId, $customerGroupId, $direction, $brandId)
    {
        $data = $this->_getResource()->getRateDataForBrand($websiteId, $customerGroupId, $direction, $brandId);
        if ($data && $data['rate_id'] != $this->getId()) {
            return false;
        }
        return true;
    }
}

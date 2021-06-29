<?php

namespace Margifox\BrandReward\Api;

/**
 * Interface BrandRewardManagementInterface
 * @api
 */
interface BrandRewardManagementInterface
{
    /**
     * Set reward points to quote
     *
     * @param int $cartId
     * @param int $brandId
     * @param string $rewardType
     * @return boolean
     */
    public function set($cartId, $brandId, $rewardType);
}

<?php

namespace Margifox\BrandReward\Model\MagentoReward;

class ConfigProvider extends \Magento\Reward\Model\ConfigProvider
{
    /**
     * It disables Reward Points block on checkout page
     * TODO: disable it in some other way. It was implemented in that way, to have all other Reward Points blocks on frontend
     *
     * @return bool
     */
    protected function isAvailable()
    {
        return false;
    }
}

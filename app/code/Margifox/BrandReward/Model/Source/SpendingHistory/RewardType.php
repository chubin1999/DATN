<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Model\Source\SpendingHistory;

class RewardType implements \Magento\Framework\Data\OptionSourceInterface
{
    const LOYALTY = 'loyalty';
    const PROMO_ALLOCATION = 'promo_allocation';

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::LOYALTY,
                'label' => __('Loyalty')
            ],
            [
                'value' => self::PROMO_ALLOCATION,
                'label' => __('Promo Allocation')
            ]
        ];
    }
}

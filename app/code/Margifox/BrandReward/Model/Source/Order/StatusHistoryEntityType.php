<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Model\Source\Order;

class StatusHistoryEntityType implements \Magento\Framework\Data\OptionSourceInterface
{
    const LOYALTY_EARNING = 'loyalty_earning';
    const LOYALTY_REDEEMING = 'loyalty_redeeming';

    const PROMO_ALLOCATION_EARNING = 'promo_allocation_earning';
    const PROMO_ALLOCATION_REDEEMING = 'promo_allocation_redeeming';

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::LOYALTY_REDEEMING,
                'label' => __('Loyalty Redeeming')
            ],
            [
                'value' => self::LOYALTY_EARNING,
                'label' => __('Loyalty Earning')
            ],
            [
                'value' => self::PROMO_ALLOCATION_EARNING,
                'label' => __('Promo Allocation Earning')
            ],
            [
                'value' => self::PROMO_ALLOCATION_REDEEMING,
                'label' => __('Promo Allocation Redeeming')
            ]
        ];
    }
}

<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Model\Source\SpendingHistory;

class ActionType implements \Magento\Framework\Data\OptionSourceInterface
{
    const PURCHASE = 'purchase';
    const ORDER_REFUNDED = 'order_refunded';
    const REDEEM = 'redeem';

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::PURCHASE,
                'label' => __('Purchase')
            ],
            [
                'value' => self::ORDER_REFUNDED,
                'label' => __('Order Refunded')
            ],
            [
                'value' => self::REDEEM,
                'label' => __('Redeem')
            ]
        ];
    }
}

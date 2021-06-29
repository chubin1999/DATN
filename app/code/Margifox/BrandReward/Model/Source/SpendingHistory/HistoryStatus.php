<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Model\Source\SpendingHistory;

class HistoryStatus implements \Magento\Framework\Data\OptionSourceInterface
{
    const ACTIVE = 1;
    const REDEEMING = 2;
    const REDEEMED = 3;
    const EXPIRED = 4;

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ACTIVE,
                'label' => __('Active')
            ],
            [
                'value' => self::REDEEMING,
                'label' => __('Redeeming')
            ],
            [
                'value' => self::REDEEMED,
                'label' => __('Redeemed')
            ],
            [
                'value' => self::EXPIRED,
                'label' => __('Expired')
            ]
        ];
    }
}

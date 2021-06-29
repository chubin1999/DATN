<?php

namespace Margifox\BrandReward\Model\Source;

class SpendingLevel implements \Magento\Framework\Data\OptionSourceInterface
{
    public const COPPER = 'copper';
    public const BRONZE = 'bronze';
    public const SILVER = 'silver';
    public const GOLD = 'gold';
    public const PLATINUM = 'platinum';

    /**
     * @return array
     */
    public function toOption()
    {
        return [
            ucfirst(self::COPPER) => self::COPPER,
            ucfirst(self::BRONZE) => self::BRONZE,
            ucfirst(self::SILVER) => self::SILVER,
            ucfirst(self::GOLD) => self::GOLD,
            ucfirst(self::PLATINUM) => self::PLATINUM,
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        foreach ($this->toOption() as $key => $value) {
            $options[] = [
                'value' => $value,
                'label' => $key
            ];
        }

        return $options ?? [];
    }
}

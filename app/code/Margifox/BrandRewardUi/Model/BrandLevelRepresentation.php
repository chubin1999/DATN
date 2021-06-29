<?php

namespace Margifox\BrandRewardUi\Model;

use Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface;

class BrandLevelRepresentation
{
    /**
     * @var CompanyBrandSpendingLevelInterface
     */
    private $brandSpendingLevel;

    /**
     * @var \Margifox\BrandReward\Provider\SpendingLevelProvider
     */
    private $spendingLevelConfigProvider;

    /**
     * @var \Margifox\Brand\Api\BrandRepositoryInterface
     */
    private $brandRepository;

    /**
     * @param \Margifox\BrandReward\Provider\SpendingLevelProvider $spendingLevelConfigProvider
     * @param \Margifox\Brand\Api\BrandRepositoryInterface $brandRepository
     */
    public function __construct(
        \Margifox\BrandReward\Provider\SpendingLevelProvider $spendingLevelConfigProvider,
        \Margifox\Brand\Api\BrandRepositoryInterface $brandRepository
    ) {
        $this->spendingLevelConfigProvider = $spendingLevelConfigProvider;
        $this->brandRepository = $brandRepository;
    }

    /**
     * @param CompanyBrandSpendingLevelInterface $brandSpendingLevel
     */
    public function setBrandSpendingLevel($brandSpendingLevel)
    {
        $this->brandSpendingLevel = $brandSpendingLevel;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $siblings = $this->getCurrentLevelSiblings();
        $items = [];
        $i = 0;
        foreach ($siblings['items'] as $key => $value) {
            $data['name'] = $key;
            $data['price'] = $value;
            $class = '';
            if ($siblings['key'] === $i) {
                $class = 'current';
            }
            if ($siblings['key'] > $i) {
                $class = 'done';
            }
            $data['class'] = $class;
            $items[] = $data;
            $i++;
        }

        return [
            'brand_name' => $this->getBrandName(),
            'brand_level' => $this->getSpendingLevel(),
            'siblings' => $items
        ];
    }

    /**
     * @return string
     */
    private function getBrandName()
    {
        return $this->brandRepository->getById($this->brandSpendingLevel->getBrandId())->getName();
    }

    /**
     * @return array
     */
    private function getCurrentLevelSiblings()
    {
        $items = $this->spendingLevelConfigProvider->getMinSalesPerMonth($this->getBrandId());

        $currentLevel = $this->getSpendingLevel();
        $keys = array_keys($items);
        $keyPosition = array_search($currentLevel, $keys);
        if ($keyPosition === false) {
            throw new \RuntimeException('Undefined brand index');
        }
        if ($keyPosition === 0) {
            return [
                'key' => 0,
                'items' => array_slice($items, 0, 3)
            ];
        }
        if ($keyPosition === (count($keys) - 1)) {
            return [
                'key' => 2,
                'items' => array_slice($items, -3)
            ];
        }

        return [
            'key' => 1,
            'items' => array_slice($items, $keyPosition - 1, 3)
        ];
    }

    /**
     * @return string
     */
    private function getSpendingLevel()
    {
        return $this->brandSpendingLevel->getSpendingLevel();
    }

    /**
     * @return int
     */
    private function getBrandId()
    {
        return $this->brandSpendingLevel->getBrandId();
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


declare(strict_types=1);

namespace Amasty\Label\Model;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\ResourceModel\Index;
use Amasty\Label\Model\ResourceModel\Labels\Collection as LabelsCollection;
use Amasty\Label\Model\ResourceModel\Labels\CollectionFactory as LabelsCollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class LabelsDataProvider
{
    /**
     * @var LabelsCollection|null
     */
    private $activeLabelCollection = null;

    /**
     * @var array|null
     */
    private $activeLabelsIds = null;

    /**
     * @var int[]
     */
    private $resolvedLabelsForProduct = [];

    /**
     * @var Index
     */
    private $labelIndex;

    /**
     * @var LabelsCollectionFactory
     */
    private $labelCollectionFactory;

    /**
     * @var bool[]
     */
    private $forParentEnabled = [];

    public function __construct(
        Index $labelIndex,
        LabelsCollectionFactory $labelCollectionFactory
    ) {
        $this->labelIndex = $labelIndex;
        $this->labelCollectionFactory = $labelCollectionFactory;
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return Labels[]
     * @throws LocalizedException
     */
    public function getLabelsByProductIdAndStoreId(int $productId, int $storeId): array
    {
        $sortedLabelsIds = $this->getLabelsIdsForProduct($productId, $storeId);
        $labelsCollection = $this->getFullLabelCollection();
        $labels = [];

        foreach ($sortedLabelsIds as $labelId) {
            if ($label = $labelsCollection->getItemById($labelId)) {
                $labels[] = $label;
            }
        }

        return $labels;
    }

    /**
     * if anyone label has setting - UseForParent - check all
     * @param int $storeId
     * @return bool
     */
    public function isLabelForParentEnabled(int $storeId): bool
    {
        if (!isset($this->forParentEnabled[$storeId])) {
            $condition[] = [
                'finset' => [$storeId]
            ];
            /** @var LabelsCollection $collection **/
            $collection = $this->labelCollectionFactory->create()
                ->addActiveFilter()
                ->addFieldToFilter('stores', $condition)
                ->addFieldToFilter(LabelInterface::USE_FOR_PARENT, 1)
                ->setPageSize(1);
            $this->forParentEnabled[$storeId] = (bool)$collection->getSize();
        }

        return $this->forParentEnabled[$storeId];
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return int[]
     * @throws LocalizedException
     */
    private function getLabelsIdsForProduct(int $productId, int $storeId): array
    {
        if (!isset($this->resolvedLabelsForProduct[$storeId][$productId])) {
            $labelsForProduct = array_map('intval', $this->labelIndex->getIdsFromIndex($productId, $storeId));
            $this->resolvedLabelsForProduct[$storeId][$productId] = $this->resortLabelsIdsByPriority($labelsForProduct);
        }

        return $this->resolvedLabelsForProduct[$storeId][$productId];
    }

    /**
     * @return LabelsCollection
     */
    protected function getFullLabelCollection(): LabelsCollection
    {
        if ($this->activeLabelCollection === null) {
            $this->activeLabelCollection = $this->labelCollectionFactory->create()
                ->addActiveFilter()
                ->setOrder('pos', 'asc')
                ->load();
        }

        return $this->activeLabelCollection;
    }

    /**
     * @return int[]
     */
    private function getAllActiveLabelsIds(): array
    {
        if ($this->activeLabelsIds === null) {
            $labelsData = $this->getFullLabelCollection()->getData();
            $this->activeLabelsIds = array_map(function ($labelData) {
                return (int)$labelData[LabelInterface::LABEL_ID];
            }, $labelsData);
        }

        return $this->activeLabelsIds;
    }

    /**
     * @param int[] $labelsIds
     *
     * @return int[]
     */
    protected function resortLabelsIdsByPriority(array $labelsIds): array
    {
        return array_intersect($this->getAllActiveLabelsIds(), $labelsIds);
    }
}

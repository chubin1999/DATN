<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Block\Label as LabelBlock;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Session;
use Magento\Framework\Profiler;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

class LabelViewer
{
    const MODE_CATEGORY = 'category';

    const MODE_PRODUCT_PAGE = 'product';

    /**
     * @var bool|null
     */
    private $showSeveralLabels = null;

    /**
     * @var int|null
     */
    private $maxLabelCount = null;

    /**
     * @var Configurable
     */
    private $productTypeConfigurable;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var \Amasty\Label\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var LabelsDataProvider
     */
    private $labelsProvider;

    /**
     * @var null|LabelBlock
     */
    private $labelBlock = null;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        Configurable $catalogProductTypeConfigurable,
        \Amasty\Base\Model\Serializer $serializer,
        Session $customerSession,
        \Amasty\Label\Helper\Config $config,
        LabelsDataProvider $labelsProvider
    ) {
        $this->productTypeConfigurable = $catalogProductTypeConfigurable;
        $this->serializer = $serializer;
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->layout = $layout;
        $this->labelsProvider = $labelsProvider;
    }

    /**
     * @param Product $product
     * @param string $mode
     * @param bool $shouldMove
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function renderProductLabel(Product $product, $mode = AbstractLabels::CATEGORY_MODE, $shouldMove = false)
    {
        $html = '';

        Profiler::start('__RenderAmastyProductLabel__');
        foreach ($this->getAppliedLabels($product, $mode, $shouldMove) as $appliedLabel) {
            $html .= $this->generateHtml($appliedLabel);
        }

        Profiler::stop('__RenderAmastyProductLabel__');

        return $html;
    }

    /**
     * @param Product $product
     * @param string $mode
     * @param bool $shouldMove
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAppliedLabels(Product $product, $mode = AbstractLabels::CATEGORY_MODE, $shouldMove = false)
    {
        $appliedItems = [];
        $appliedLabelIds = [];
        $applied = 0;
        $maxLabelCount = $this->getMaxLabelCount();
        $productLabels = $this->labelsProvider->getLabelsByProductIdAndStoreId(
            (int)$product->getId(),
            $product->getStoreId()
        );

        foreach ($productLabels as $label) {
            if ($applied == $maxLabelCount) {
                break;
            }

            if ($this->validateNonProductDependConditions($label, $applied)) {
                continue;
            }

            $label->setShouldMove($shouldMove);
            $label->init($product, $mode);
            if ($this->addLabelToApplied($label, $appliedLabelIds)) {
                $applied++;
                $appliedItems[] = $label;
            }
        }

        /* apply label from child products*/
        if ($applied !== $maxLabelCount
            && in_array($product->getTypeId(), [Grouped::TYPE_CODE, Configurable::TYPE_CODE])
            && $this->labelsProvider->isLabelForParentEnabled($product->getStoreId())
        ) {
            $usedProds = $this->getUsedProducts($product);

            foreach ($usedProds as $child) {
                $childLabels = $this->labelsProvider->getLabelsByProductIdAndStoreId(
                    (int)$child->getId(),
                    $child->getStoreId()
                );

                foreach ($childLabels as $label) {
                    if ($applied == $maxLabelCount) {
                        break;
                    }

                    if (!$label->getUseForParent()
                        || $this->validateNonProductDependConditions($label, $applied)
                        || array_key_exists($label->getId(), $appliedLabelIds) // (remove duplicated)
                    ) {
                        continue;
                    }

                    $label->setShouldMove($shouldMove);
                    $label->init($child, $mode, $product);

                    if ($this->addLabelToApplied($label, $appliedLabelIds)) {
                        $applied++;
                        $appliedItems[] = $label;
                    }
                }
            }
        }

        return $appliedItems;
    }

    /**
     * @param \Amasty\Label\Model\Labels $label
     * @param $appliedLabelIds
     *
     * @return bool
     */
    private function addLabelToApplied(Labels $label, &$appliedLabelIds)
    {
        $position = $label->getMode() == 'cat' ? $label->getCatPos() : $label->getProdPos();
        if (!$this->isShowSeveralLabels()) {
            if (array_search($position, $appliedLabelIds) !== false) {
                return false;
            }
        }

        $appliedLabelIds[$label->getId()] = $position;

        return true;
    }

    /**
     * @param \Amasty\Label\Model\Labels $label
     * @param bool $applied
     * @return bool
     */
    private function validateNonProductDependConditions(Labels $label, &$applied)
    {
        if ($label->getIsSingle() === '1' && $applied) {
            return true;
        }

        // need this condition, because in_array returns true for NOT LOGGED IN customers
        if ($label->getCustomerGroupEnabled()
            && !$this->checkCustomerGroupCondition($label)
        ) {
            return true;
        }

        if (!$label->checkDateRange()) {
            return true;
        }

        return false;
    }

    /**
     * @param Labels $label
     * @return bool
     */
    private function checkCustomerGroupCondition(Labels $label)
    {
        if (!$label->hasData(LabelInterface::CUSTOMER_GROUP_VALID)) {
            $groups = $label->getData('customer_group_ids');
            if ($groups === '') {
                return true;
            }

            $groups = $this->serializer->unserialize($groups);
            $customerGroupValid = in_array(
                (int)$this->customerSession->getCustomerGroupId(),
                $groups
            );
            $label->setData(LabelInterface::CUSTOMER_GROUP_VALID, $customerGroupValid);
        }

        return $label->getData(LabelInterface::CUSTOMER_GROUP_VALID);
    }

    /**
     * generate block with label configuration
     * @param Labels $label
     * @return string
     */
    private function generateHtml(Labels $label)
    {
        if ($this->labelBlock === null) {
            $this->labelBlock = $this->layout->createBlock(LabelBlock::class);
        }

        return $this->labelBlock->setLabel($label)->toHtml();
    }

    /**
     * @param Product $product
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    private function getUsedProducts(Product $product)
    {
        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            return $this->productTypeConfigurable->getUsedProducts($product);
        } else { // product is grouped
            return $product->getTypeInstance(true)->getAssociatedProducts($product);
        }
    }

    /**
     * @return bool
     */
    private function isShowSeveralLabels()
    {
        if ($this->showSeveralLabels === null) {
            $this->showSeveralLabels = $this->config->isShowSeveralOnPlace();
        }

        return (bool)$this->showSeveralLabels;
    }

    /**
     * @return int
     */
    private function getMaxLabelCount()
    {
        if ($this->maxLabelCount === null) {
            $this->maxLabelCount = $this->config->getMaxLabels();
        }

        return $this->maxLabelCount;
    }
}

<?php

namespace Margifox\BrandReward\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class PointsRedeem extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Margifox\BrandReward\Model\Source\SpendingLevel
     */
    protected $spendingLevel;

    /**
     * @var BrandColumn
     */
    protected $brandRenderer;

    /**
     * @param \Margifox\BrandReward\Model\Source\SpendingLevel $spendingLevel
     * @param Context $context
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        \Margifox\BrandReward\Model\Source\SpendingLevel $spendingLevel,
        Context $context,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->spendingLevel = $spendingLevel;
        parent::__construct($context, $data, $secureRenderer);
    }

    protected function _prepareToRender()
    {
        $this->addColumn('brand', [
            'label' => __('Brand'),
            'renderer' => $this->getBrandRenderer()
        ]);
        $this->addColumn('value', [
            'label' => __('Value'),
            'class' => 'required-entry'
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @param DataObject $row
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $tax = $row->getBrand();
        if ($tax !== null) {
            $options['option_' . $this->getBrandRenderer()->calcOptionHash($tax)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    private function getBrandRenderer(): BrandColumn
    {
        if (!$this->brandRenderer) {
            $this->brandRenderer = $this->getLayout()->createBlock(
                BrandColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->brandRenderer;
    }
}

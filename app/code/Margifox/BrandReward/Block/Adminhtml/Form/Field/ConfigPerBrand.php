<?php

namespace Margifox\BrandReward\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class ConfigPerBrand extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
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

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('brand', [
            'label' => __('Brand'),
            'renderer' => $this->getBrandRenderer()
        ]);

        foreach ($this->spendingLevel->toOption() as $name => $value) {
            $this->addColumn(
                $value,
                [
                    'label' => $name,
                    'class' => 'required-entry'
                ]
            );
        }

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @param DataObject $row
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $brand = $row->getBrand();
        if ($brand !== null) {
            $options['option_' . $this->getBrandRenderer()->calcOptionHash($brand)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return BrandColumn
     * @throws \Magento\Framework\Exception\LocalizedException
     */
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

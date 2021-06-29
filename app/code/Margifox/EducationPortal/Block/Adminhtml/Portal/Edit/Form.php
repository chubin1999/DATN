<?php

namespace Margifox\EducationPortal\Block\Adminhtml\Portal\Edit;

use Magento\Catalog\Model\Product;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    )
    {
        $this->_systemStore = $systemStore;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid_form');
        $this->setTitle(__('Portal Information'));
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('portal_grid');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
        );

        $form->setHtmlIdPrefix('portal_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'status',
            'checkbox',
            [
                'name' => 'status',
                'class' => 'admin__actions-switch-checkbox',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Enable'),
                'value' => $model['status'] ? $model['status'] : 0,
                'checked' => $model['status'] ? $model['status'] : 0,
                'after_element_js' => '
                    <label class="admin__actions-switch-label" id="status_field">
                        <span class="admin__actions-switch-text" data-text-on="' . __('Yes') . '" data-text-off="' . __('No') . '"></span>
                    </label>
                    <script type="text/javascript">
                        require(["jquery"], function($){
                            $("#status_field").click(function() {
                                if($("#portal_status").is(":checked")) {
                                    $("#portal_status").val("0");
                                    $("#portal_status").prop(\'checked\', false);
                                } else {
                                    $("#portal_status").val("1");
                                    $("#portal_status").prop(\'checked\', true);
                                }
                            });
                        });
                    </script>
'
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );

        $fieldset->addField(
            'description',
            'textarea',
            ['name' => 'description', 'label' => __('Description'), 'title' => __('Description'), 'required' => false]
        );

        $fieldset->addField(
            'video',
            'textarea',
            ['name' => 'video', 'label' => __('Video'), 'title' => __('Video'), 'required' => false]
        );

        $fieldset->addField(
            'pdf',
            'file',
            [
                'name' => 'pdf',
                'label' => __('Pdf'),
                'title' => __('Pdf'),
                'required' => false,
                'after_element_html' => $model['pdf'] ? '<p style="margin-top: 10px">' . $model['pdf'] . '</p>' : ''
            ]
        );

        $fieldset->addField(
            'brand',
            'multiselect',
            [
                'name' => 'brand',
                'label' => __('Brand'),
                'title' => __('Brand'),
                'values' => $this->getBrandOptions(),
                'required' => false
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get brand options
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getBrandOptions()
    {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, 'brand');
        $options = $attribute->getSource()->getAllOptions();
        if (count($options)) {
            foreach ($options as $key => $option) {
                if (!$option['value']) {
                    unset($options[$key]);
                }
            }
        }
        return $options;
    }
}

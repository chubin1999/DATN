<?php

namespace Margifox\BrandReward\Model\Adminhtml\Reward\Rate\Edit;

class Form extends \Magento\Reward\Block\Adminhtml\Reward\Rate\Edit\Form
{
    /**
     * @var \Margifox\Brand\Model\Brand\BrandOptionsProvider
     */
    private $brandOptionsProvider;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Reward\Model\Source\WebsiteFactory $websitesFactory
     * @param \Magento\Reward\Model\Source\Customer\GroupsFactory $groupsFactory
     * @param \Margifox\Brand\Model\Brand\BrandOptionsProvider $brandOptionsProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Reward\Model\Source\WebsiteFactory $websitesFactory,
        \Magento\Reward\Model\Source\Customer\GroupsFactory $groupsFactory,
        \Margifox\Brand\Model\Brand\BrandOptionsProvider $brandOptionsProvider,
        array $data = []
    ) {
        $this->brandOptionsProvider = $brandOptionsProvider;
        parent::__construct($context, $registry, $formFactory, $websitesFactory, $groupsFactory, $data);
    }

    /**
     * @return \Magento\Reward\Block\Adminhtml\Reward\Rate\Edit\Form
     */
    protected function _prepareForm()
    {
        $form = parent::_prepareForm();

        $fieldset = $form->getForm()->getElement('base_fieldset');
        $fieldset->addField(
            'brand_id',
            'select',
            [
                'name'     => 'brand_id',
                'label'    => __('Brand'),
                'title'    => __('Brand'),
                'values' => $this->brandOptionsProvider->toOptionArray()
            ]);

        return $form;
    }
}

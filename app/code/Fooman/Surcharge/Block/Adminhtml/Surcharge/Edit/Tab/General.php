<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Block\Adminhtml\Surcharge\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    private $systemStore;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Tax\Model\TaxClass\Source\Product
     */
    private $productTaxClassSource;

    /**
     * @var \Magento\Customer\Model\Customer\Source\Group
     */
    private $groupSource;

    /**
     * @var \Fooman\Surcharge\Model\System\SurchargeBasis
     */
    private $surchargeBasis;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    private $countryCollection;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    private $regionCollection;

    private $countries;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource
     * @param \Magento\Customer\Model\Customer\Source\Group $groupSource
     * @param \Fooman\Surcharge\Model\System\SurchargeBasis $surchargeBasis
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource,
        \Magento\Customer\Model\Customer\Source\Group $groupSource,
        \Fooman\Surcharge\Model\System\SurchargeBasis $surchargeBasis,
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection,
        \Magento\Directory\Model\ResourceModel\Region\Collection $regionCollection,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->coreRegistry = $coreRegistry;
        $this->productTaxClassSource = $productTaxClassSource;
        $this->groupSource = $groupSource;
        $this->surchargeBasis = $surchargeBasis;
        $this->countryCollection = $countryCollection;
        $this->regionCollection = $regionCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore -- Magento Core Use
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('general', ['legend' => __('General Settings')]);

        $fieldset->addField(
            'description',
            'text',
            [
                'label' => __('Description'),
                'title' => __('Description'),
                'name' => 'description',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => ['1' => __('Active'), '0' => __('Inactive')]
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'select',
                [
                    'label' => __('Store'),
                    'title' => __('Store'),
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                    'name' => 'store_id',
                    'required' => true,
                    'renderer' => $this->getLayout()->createBlock(
                        \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
                    )
                ]
            );
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name' => 'store_id',
                    'value' => $this->_storeManager->getStore(true)->getId()
                ]
            );
        }

        $fieldset->addField(
            'tax_class_id',
            'select',
            [
                'label' => __('Tax Rate'),
                'title' => __('Tax Rate'),
                'name' => 'tax_class_id',
                'required' => true,
                'values' => $this->productTaxClassSource->getAllOptions()
            ]
        );

        $fieldset->addField(
            'tax_inclusive',
            'select',
            [
                'label' => __('Amounts are '),
                'title' => __('Amounts are '),
                'name' => 'tax_inclusive',
                'required' => false,
                'options' => [
                    0 => __('Exclusive of Tax'),
                    1 => __('Inclusive of Tax'),
                ]
            ]
        );

        $fieldset->addField(
            'apply_group_filter',
            'select',
            [
                'label' => __('Apply Group Filter'),
                'title' => __('Apply Group Filter'),
                'name' => 'apply_group_filter',
                'required' => false,
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ]
            ]
        );

        $fieldset->addField(
            'groups',
            'multiselect',
            [
                'label' => __('Groups'),
                'title' => __('Groups'),
                'name' => 'groups[]',
                'required' => false,
                'values' => $this->getGroupOptions()
            ]
        );

        $fieldset->addField(
            'apply_region_filter',
            'select',
            [
                'label' => __('Apply Country/Region Filters'),
                'title' => __('Apply Country/Region Filters'),
                'name' => 'apply_region_filter',
                'required' => false,
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ]
            ]
        );

        $fieldset->addField(
            'region_filter_address_type',
            'select',
            [
                'label' => __('Address'),
                'title' => __('Address'),
                'name' => 'region_filter_address_type',
                'required' => false,
                'options' => [
                    \Fooman\Surcharge\Model\SurchargeRestrictor::ADDRESS_TYPE_SHIP => __('Shipping Address'),
                    \Fooman\Surcharge\Model\SurchargeRestrictor::ADDRESS_TYPE_BILL => __('Billing Address'),
                ]
            ]
        );

        $fieldset->addField(
            'countries',
            'multiselect',
            [
                'label' => __('Countries'),
                'title' => __('Countries'),
                'name' => 'countries[]',
                'required' => false,
                'values' => $this->getCountryOptions()
            ]
        );

        $fieldset->addField(
            'regions',
            'multiselect',
            [
                'label' => __('Regions'),
                'title' => __('Regions'),
                'name' => 'regions[]',
                'required' => false,
                'values' => $this->getRegionOptions(),
                'note' => __('Leave selection empty to apply to all regions of selected countries above.')
            ]
        );

        $fieldset->addField(
            'based_on',
            'multiselect',
            [
                'label' => __('Based On'),
                'title' => __('Based On'),
                'name' => 'based_on[]',
                'required' => false,
                'values' => $this->surchargeBasis->toOptionArray(),
                'value' => \Fooman\Surcharge\Model\System\SurchargeBasis::BASED_ON_SUBTOTAL
            ]
        );

        $registry = $this->coreRegistry->registry('fooman_surcharge');
        $formData = $registry->getData();
        $ruleData = json_decode($registry->getDataRule(), true);

        if ($ruleData) {
            $keys = [
                'apply_group_filter',
                'groups',
                'based_on',
                'apply_region_filter',
                'countries',
                'regions',
                'region_filter_address_type'
            ];
            foreach ($keys as $key) {
                if (isset($ruleData[$key])) {
                    $formData[$key] = $ruleData[$key];
                }
            }
        }

        $form->addValues($formData);
        $form->setFieldNameSuffix('general');
        $this->setForm($form);
    }

    private function getGroupOptions()
    {
        $result = [];
        $groups = $this->groupSource->toOptionArray();
        foreach ($groups as $group) {
            if ($group['value'] != \Magento\Customer\Api\Data\GroupInterface::CUST_GROUP_ALL) {
                $result[] = ['label' => $group['label'], 'value' => $group['value']];
            }
        }
        return $result;
    }

    private function getCountryOptions()
    {
        if (null === $this->countries) {
            $this->countries = $this->countryCollection->toOptionArray(false);
        }
        return $this->countries;
    }

    private function getRegionOptions()
    {
        return $this->sortRegions($this->regionCollection->toOptionArray());
    }

    private function sortRegions($regions)
    {
        usort($regions, function ($a, $b) {
            if (!isset($a['country_id'])) {
                return 1;
            }
            if (!isset($b['country_id'])) {
                return -1;
            }
            if ($a['country_id'] === $b['country_id']) {
                return strnatcmp($a['title'], $b['title']);
            }
            return strnatcmp($a['country_id'], $b['country_id']);
        });

        $regionsByCountry = [];
        foreach ($regions as $region) {
            if (isset($region['country_id'])) {
                $countryKey = array_search(
                    $region['country_id'],
                    array_column($this->getCountryOptions(), 'value')
                );
                if (!isset($regionsByCountry[$region['country_id']]['label'])) {
                    $regionsByCountry[$region['country_id']]['label'] = $this->countries[$countryKey]['label'];
                }
                $regionsByCountry[$region['country_id']]['value'][] = $region;
            }
        }
        return $regionsByCountry;
    }
}

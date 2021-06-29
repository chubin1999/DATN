<?php

namespace Hty\Checkout\Block;

use Magento\Checkout\Block\Checkout\LayoutProcessor as Subject;

/**
 * Class LayoutProcessor
 *
 * @package Hty\Checkout\Block
 */
class LayoutProcessor
{
     /**
     * @param Subject $subject
     * @param array $jsLayout
     * @return array
     */
     public function afterProcess(Subject$subject, array  $jsLayout)
     {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street'] = [
        'component' => 'Magento_Ui/js/form/components/group',
        'label' => __('Address'),
        'required' => true,
        'dataScope' => 'shippingAddress.street',
        'provider' => 'checkoutProvider',
        'sortOrder' => 70,
        'type' => 'group',
        'additionalClasses' => 'streetCustom',
        'children' => [
            [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input'
                ],
                'dataScope' => '0',
                'provider' => 'checkoutProvider',
                'validation' => [
                    'required-entry' => true,
                ],
                'additionalClasses' => 'additional',
                'label' => __('Street address')
            ],
            [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input'
                ],
                'dataScope' => '1',
                'provider' => 'checkoutProvider',
                'validation' => false,
                'label' => __('Secondary address')
            ]
        ]];
            $configuration = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];
            foreach ($configuration as $paymentGroup => $groupConfig) {
                if (isset($groupConfig['component']) AND $groupConfig['component'] === 'Magento_Checkout/js/view/billing-address') {

                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['street'] =  [
                    'component' => 'Magento_Ui/js/form/components/group',
                    'label' => __('Address'),
                    'required' => true,
                    'dataScope' => 'shippingAddress.street',
                    'provider' => 'checkoutProvider',
                    'sortOrder' => 70,
                    'type' => 'group',
                    'additionalClasses' => 'streetCustom',
                    'children' => [
                        [
                            'component' => 'Magento_Ui/js/form/element/abstract',
                            'config' => [
                                'customScope' => 'shippingAddress',
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/input'
                            ],
                            'dataScope' => '0',
                            'provider' => 'checkoutProvider',
                            'validation' => [
                                'required-entry' => true,
                            ],
                            'additionalClasses' => 'additional',
                            'label' => __('Street address')
                        ],
                        [
                            'component' => 'Magento_Ui/js/form/element/abstract',
                            'config' => [
                                'customScope' => 'shippingAddress',
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/input'
                            ],
                            'dataScope' => '1',
                            'provider' => 'checkoutProvider',
                            'validation' => false,
                            'label' => __('Secondary address')
                        ]
                    ]];
                }
            }
         return $jsLayout;
     }
 }

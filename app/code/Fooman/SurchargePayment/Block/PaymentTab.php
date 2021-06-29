<?php
/**
 * @copyright Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\SurchargePayment\Block;

use Fooman\Surcharge\Model\SurchargeCalculation;
use Fooman\SurchargePayment\Model\PaymentConfig;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class PaymentTab extends Generic
{

    /**
     * @var PaymentConfig
     */
    private $paymentModelConfig;

    /**
     * @param Context      $context
     * @param Registry                  $registry
     * @param FormFactory          $formFactory
     * @param PaymentConfig $paymentModelConfig
     * @param array                                        $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        PaymentConfig $paymentModelConfig,
        array $data = []
    ) {
        $this->paymentModelConfig = $paymentModelConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration -- Magento 2 core use
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('general', ['legend' => __('Payment Surcharge Settings')]);

        $fieldset->addField('payment', 'multiselect', [
            'label' => __('Payment Methods'),
            'title' => __('Payment Methods'),
            'name' => 'payment[]',
            'required' => true,
            'values' => $this->getListOfPaymentMethodsGrouped()
        ]);

        $fieldset->addField('min', 'text', [
            'label' => __('Order Minimum'),
            'title' => __('Order Minimum'),
            'name' => 'min',
            'required' => false,
        ]);

        $fieldset->addField('max', 'text', [
            'label' => __('Order Maximum'),
            'title' => __('Order Maximum'),
            'name' => 'max',
            'required' => false,
        ]);

        $fieldset->addField('calculation_mode', 'select', [
            'label' => __('Surcharge Calculation Mode'),
            'title' => __('Surcharge Calculation Mode'),
            'name' => 'calculation_mode',
            'required' => true,
            'options' => [
                '' => '',
                SurchargeCalculation::FIXED => __('Fixed'),
                SurchargeCalculation::PERCENT => __('Percent'),
                SurchargeCalculation::FIXED_PLUS_PERCENT => __('Fixed + Percent'),
                SurchargeCalculation::FIXED_MINIMUM => __('Maximum of Fixed or Percent'),
            ]
        ]);

        $fieldset->addField('rate', 'text', [
            'label' => __('Surcharge %'),
            'title' => __('Surcharge %'),
            'name' => 'rate',
            'required' => true,
        ]);

        $fieldset->addField('fixed', 'text', [
            'label' => __('Surcharge Fixed Cost'),
            'title' => __('Surcharge Fixed Cost'),
            'name' => 'fixed',
            'required' => true,
        ]);

        $registry = $this->_coreRegistry->registry('fooman_surcharge');

        if ($registry) {
            $formData = json_decode($registry->getDataRule(), true);
            $form->addValues($formData);
        }

        $form->setFieldNameSuffix('payment');
        $this->setForm($form);
    }

    private function getListOfPaymentMethodsGrouped()
    {
        return $this->paymentModelConfig->getGroupedList();
    }
}

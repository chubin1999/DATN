<?php
/**
 * @copyright Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\SurchargePayment\Model;

use Fooman\Surcharge\Api\Data\TypeInterface;
use Fooman\Surcharge\Api\SurchargeInterface;
use Fooman\Surcharge\Helper\SurchargeConfig;
use Fooman\Surcharge\Model\SurchargeCalculationFactory;
use Fooman\Totals\Model\QuoteAddressTotal;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;

class PaymentType implements TypeInterface
{
    /**
     * @var string
     */
    private $type = 'payment';

    /**
     * @var SurchargeConfig
     */
    private $surchargeConfigHelper;

    /**
     * @var SurchargeCalculationFactory
     */
    private $surchargeCalculationFactory;

    /**
     * @param SurchargeCalculationFactory $surchargeCalculationFactory
     * @param SurchargeConfig            $surchargeConfigHelper
     */
    public function __construct(
        SurchargeCalculationFactory $surchargeCalculationFactory,
        SurchargeConfig $surchargeConfigHelper
    ) {
        $this->surchargeCalculationFactory = $surchargeCalculationFactory;
        $this->surchargeConfigHelper = $surchargeConfigHelper;
    }

    /**
     * @param  SurchargeInterface $surcharge
     * @param  CartInterface    $quote
     *
     * @return QuoteAddressTotal[]
     * @throws LocalizedException
     */
    public function calculate(
        SurchargeInterface $surcharge,
        CartInterface $quote,
        ShippingAssignmentInterface $shippingAssignment
    ) {
        $config = $this->surchargeConfigHelper->getConfig($surcharge);

        $paymentMethods = $config->getPayment();
        if (!$paymentMethods) {
            return [];
        }

        if (is_string($paymentMethods)) {
            $paymentMethods = [$paymentMethods];
        }

        $surchargeCalculation = $this->surchargeCalculationFactory
            ->create(['quote' => $quote, 'surcharge' => $surcharge, 'assignment' => $shippingAssignment]);

        if ($this->surchargeApplies($quote, $paymentMethods)) {
            return [$surchargeCalculation->processTotals()];
        }

        return [];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __('Payment Surcharge');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param CartInterface $quote
     * @param                                       $paymentMethods
     *
     * @return bool
     */
    public function surchargeApplies(CartInterface $quote, $paymentMethods)
    {
        $currentPaymentMethod = $quote->getPayment()->getMethod();
        if (!$currentPaymentMethod && $quote->getPayment()->getAdditionalInformation('button') === 1) {
            $currentPaymentMethod = 'paypal_express';
        }
        return in_array($currentPaymentMethod, $paymentMethods);
    }
}

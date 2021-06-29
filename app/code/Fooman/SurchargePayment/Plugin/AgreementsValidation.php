<?php
/**
 * @copyright Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\SurchargePayment\Plugin;

use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\CheckoutAgreements\Model\Checkout\Plugin\Validation;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class AgreementsValidation
{

    public function aroundBeforeSavePaymentInformation(
        Validation $plugin,
        \Closure $proceed,
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {

        try {
            $proceed($subject, $cartId, $paymentMethod, $billingAddress);
        } catch (CouldNotSaveException $e) {
            // Disable Exception for Terms and Conditions having been ticked
            // if we are in preview mode
            $extraData = $paymentMethod->getAdditionalData();
            if (!isset($extraData['fooman_payment_surcharge_preview'])
                || $extraData['fooman_payment_surcharge_preview'] !== 'true'
                || $this->isNotTargetedExceptionMessage($e)
            ) {
                throw $e;
            }
        }
    }

    private function isNotTargetedExceptionMessage($e)
    {
        $msg = $e->getMessage();
        return $msg != __('Please agree to all the terms and conditions before placing the order.')
            && $msg != __(
                "The order wasn't placed. "
                . "First, agree to the terms and conditions, then try placing your order again."
            );
    }
}

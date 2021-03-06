<?php
/**
 * @copyright Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\SurchargePayment\Plugin;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\OfflinePayments\Model\Purchaseorder;

class PurchaseOrderValidation
{

    public function aroundValidate(
        Purchaseorder $subject,
        \Closure $proceed
    ) {
        try {
            $proceed();
        } catch (LocalizedException $e) {
            // Disable ValidationException for Purchase Orders
            // if we are in preview mode
            $info = $subject->getInfoInstance();
            if ($info->getFoomanPaymentSurchargePreview() !== true
                || $e->getMessage() != __('Purchase order number is a required field.')
            ) {
                throw $e;
            }
        }
    }

    public function aroundAssignData(
        Purchaseorder $subject,
        \Closure $proceed,
        DataObject $data
    ) {
        $proceed($data);
        $extraData = $data->getAdditionalData();
        $info = $subject->getInfoInstance();
        if (isset($extraData['fooman_payment_surcharge_preview'])
            && $extraData['fooman_payment_surcharge_preview'] === 'true') {
            $info->setData('fooman_payment_surcharge_preview', true);
        } else {
            $info->setData('fooman_payment_surcharge_preview', false);
        }
        return $subject;
    }
}

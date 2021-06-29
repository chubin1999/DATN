/*
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/set-payment-information',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/get-totals',
    'jquery',
    'Fooman_Surcharge/js/should-refresh',
    'ko'
], function (
    Component,
    quote,
    setPaymentInformation,
    defaultPayment,
    checkoutData,
    getTotals,
    $,
    shouldRefresh,
    ko
) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();

            quote.billingAddress.subscribe(function (newValue) {
                if (quote.getPaymentMethod()()
                    && quote.getPaymentMethod()().method
                    && $('#'+quote.getPaymentMethod()().method).length
                    && shouldRefresh(checkoutData, newValue)
                ) {
                    var paymentMethodUI = ko.dataFor($('#' + quote.getPaymentMethod()().method)[0]);
                    var paymentMethodSubmit = paymentMethodUI.getData();

                    paymentMethodSubmit.additional_data = $.extend(
                        true,
                        paymentMethodSubmit.additional_data,
                        {
                            'fooman_payment_surcharge_preview': "true",
                            'buckaroo_skip_validation': "true"
                        }
                    );
                    $.when(setPaymentInformation(defaultPayment.messageContainer, paymentMethodSubmit))
                        .done(function () {
                            getTotals([], false);
                        });
                }
            }, this);
        }
    });
});

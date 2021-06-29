/*
 * @copyright Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
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
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'jquery',
    'Fooman_SurchargePayment/js/should-refresh',
    'Fooman_SurchargePayment/js/should-refresh-now',
    'uiRegistry',
    'ko'
], function (
    Component,
    quote,
    setPaymentInformation,
    defaultPayment,
    checkoutData,
    getTotals,
    totalsProcessor,
    $,
    shouldRefresh,
    shouldRefreshNow,
    registry,
    ko
) {
    'use strict';

    registry.set('fooman_delayed_refresh_needed', false);

    return Component.extend({
        initialize: function () {
            this._super();

            quote.paymentMethod.subscribe(function (newValue) {
                if (shouldRefresh(checkoutData, newValue)) {
                    if ($('#'+newValue.method).length ) {
                        var paymentMethodUI = ko.dataFor($('#' + newValue.method)[0]);
                        var paymentMethodSubmit = paymentMethodUI.getData();
                    } else {
                        //on first render we can't retrieve the knockout context
                        //Clone first as we need to have the data without the title for submitting
                        var paymentMethodSubmit = JSON.parse(JSON.stringify(newValue));
                        delete paymentMethodSubmit['title'];
                    }
                    paymentMethodSubmit.additional_data = $.extend(
                        true,
                        paymentMethodSubmit.additional_data,
                        {
                            'fooman_payment_surcharge_preview': "true",
                            'buckaroo_skip_validation': "true"
                        }
                    );
                    if (shouldRefreshNow(checkoutData)) {
                        registry.set('fooman_delayed_refresh_needed', false);
                        $.when(setPaymentInformation(defaultPayment.messageContainer, paymentMethodSubmit))
                            .done(function () {
                                getTotals([], false);
                            });
                    } else {
                        registry.set('fooman_delayed_refresh_needed', paymentMethodSubmit);
                    }
                }
            }, this);
        }
    });
});

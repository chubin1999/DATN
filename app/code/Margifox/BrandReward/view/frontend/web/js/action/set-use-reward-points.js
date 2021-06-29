/**
 * @api
 */
define([
    'jquery',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals'
], function (
    $,
    urlBuilder,
    storage,
    errorProcessor,
    messageList,
    $t,
    fullScreenLoader,
    getPaymentInformationAction,
    totals
) {
    'use strict';

    return function (brandId, rewardType) {
        if (brandId === undefined) {
            return;
        }
        var message = $t('Your reward point was successfully applied');

        messageList.clear();
        fullScreenLoader.startLoader();

        var url = urlBuilder.createUrl('/brand-reward/mine/use-reward/brand/:brandId/type/:rewardType', {
            brandId: brandId,
            rewardType: rewardType
        });
        storage
            .post(url, {})
            .done(function (response) {
                var deferred;

                if (response) {
                    deferred = $.Deferred();
                    totals.isLoading(true);
                    getPaymentInformationAction(deferred);
                }
                $.when(deferred).done(function () {
                    totals.isLoading(false);
                });
                messageList.addSuccessMessage({
                    'message': message
                });
            })
            .fail(function (response) {
                totals.isLoading(false);
                errorProcessor.process(response);
            })
            .always(function () {
                fullScreenLoader.stopLoader();
            });
    };
});

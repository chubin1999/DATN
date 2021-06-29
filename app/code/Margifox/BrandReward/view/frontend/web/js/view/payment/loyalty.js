define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'Margifox_BrandReward/js/action/set-use-reward-points',
    'jquery',
    'Magento_Reward/js/action/remove-points-from-summary'
], function (Component, quote, $t, setUseRewardPointsAction, $, removeRewardPointsAction) {
    'use strict';

    var rewardConfig = window.checkoutConfig.payment.rewards;

    return Component.extend({
        defaults: {
            template: 'Margifox_BrandReward/payment/loyalty'
        },
        brands: rewardConfig.brands,
        isAvailable: function () {
            var subtotal = parseFloat(quote.totals()['grand_total']),
                rewardUsedAmount = parseFloat(quote.totals()['extension_attributes']['base_reward_currency_amount']);

            return rewardConfig.isAvailable && subtotal > 0 /*&& rewardUsedAmount <= 0*/;
        },

        useRewardPoints: function (brand) {
            var elementSelector = 'input[name="reward[' + brand.id + '].brand"]';
            var inputElement = $(elementSelector).first();
            inputElement.trigger('click');
            var isChecked = inputElement.is(':checked');
            if (isChecked) {
                setUseRewardPointsAction(brand.id, 'loyalty');
            } else {
                var url = window.checkoutConfig.review.brand_reward.removeUrl
                    + '?_referer=' + window.location.hash.substr(1)
                    + '&brand_id=' + brand.id
                    + '&type=loyalty';
                removeRewardPointsAction(url);
            }
        }
    });
});

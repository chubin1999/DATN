define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'Margifox_BrandReward/js/action/set-use-reward-points',
    'jquery',
    'Magento_Reward/js/action/remove-points-from-summary'
], function (Component, quote, $t, setUseRewardPointsAction, $, removeRewardPointsAction, urlBuilder) {
    'use strict';

    var promoAllocations = window.checkoutConfig.payment.promo_allocations;

    return Component.extend({
        defaults: {
            template: 'Margifox_BrandReward/payment/promo_allocation'
        },
        brands: promoAllocations.brands,
        isAvailable: function () {
            var subtotal = parseFloat(quote.totals()['grand_total']),
                rewardUsedAmount = parseFloat(quote.totals()['extension_attributes']['base_reward_currency_amount']);

            return promoAllocations.isAvailable && subtotal > 0 /*&& rewardUsedAmount <= 0*/;
        },

        useRewardPoints: function (brand) {
            var elementSelector = 'input[name="promo_allocation[' + brand.id + '].brand"]';
            var inputElement = $(elementSelector).first();
            inputElement.trigger('click');
            var isChecked = inputElement.is(':checked');
            if (isChecked) {
                setUseRewardPointsAction(brand.id, 'promo_allocation');
            } else {
                var url = window.checkoutConfig.review.brand_reward.removeUrl
                    + '?_referer=' + window.location.hash.substr(1)
                    + '&brand_id=' + brand.id
                    + '&type=promo_allocation';
                removeRewardPointsAction(url);
            }
        }
    });
});

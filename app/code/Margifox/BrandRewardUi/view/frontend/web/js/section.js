define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (ko, Component, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Margifox_BrandRewardUi/brand_reward',
        },

        initialize: function () {
            //TODO: delete force reload
            customerData.reload(['brand_reward_section']);

            this._super();
            var brandRewardSection = customerData.get('brand_reward_section');
            this.brandRewards = brandRewardSection().brand_rewards;
        },

        labelForBrand: function (brandName) {
            return brandName + ' Stockist reward level';
        },

        getChildItems: function (siblings) {
            console.log(siblings);
        },

        labelForPrice: function (price) {
            return 'Spend $' + price;
        },

        labelForLevel: function (levelName) {
            return levelName.charAt(0).toUpperCase() + levelName.slice(1);
        }
    });
});

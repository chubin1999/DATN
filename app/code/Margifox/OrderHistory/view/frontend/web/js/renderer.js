define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage'
], function (ko, Component, urlBuilder, storage) {
    'use strict';
    return Component.extend({
        defaults: {template: 'Margifox_OrderHistory/order'},
        orders: ko.observableArray([]),
        initialize: function () {
            this._super();
            this.getOrders();
        },

        getOrders: function () {
            var self = this;
            var serviceUrl = urlBuilder.build('margifox_orders/orders/index');

            storage.post(serviceUrl, '').done(function (response) {
                self.orders.push(JSON.parse(response));
            }).fail(function (response) {
                console.log(response);
            });
        }
    });
});

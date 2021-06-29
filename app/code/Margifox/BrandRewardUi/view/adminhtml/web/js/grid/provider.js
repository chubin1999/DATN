/**
 * @api
 */
define([
    'Magento_Ui/js/grid/provider'
], function (provider) {
    'use strict';

    return provider.extend({

        /**
         * Reloads data with order id parameter
         *
         * @returns {Promise} Reload promise object.
         */
        reload: function (options) {
            var url = window.location.href;
            var searchTerm = 'order_id/';
            this.params.order_id = parseInt(url.substring(url.lastIndexOf(searchTerm) + searchTerm.length));
            var request = this.storage().getData(this.params, options);

            this.trigger('reload');

            request
                .done(this.onReload)
                .fail(this.onError.bind(this));

            return request;
        }
    });
});

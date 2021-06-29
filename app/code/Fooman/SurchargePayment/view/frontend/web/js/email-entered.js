/*
 * @copyright Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
define([
    'Fooman_SurchargePayment/js/payment-submit'
], function (
    paymentSubmit
) {
    'use strict';

    return function (target) {
        return target.extend({
            emailHasChanged: function () {
                this._super();
                paymentSubmit().executeDelayedRefreshIfNeeded();
            }
        });
    }

});
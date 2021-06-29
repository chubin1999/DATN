/*
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function (Component, quote) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Fooman_Surcharge/summary/surcharge_tax'
            },
            totals: quote.getTotals(),

            getPureValue: function () {
                for (var i in this.totals().total_segments) {
                    if (this.totals().total_segments[i].code === 'fooman_surcharge_tax') {
                        return this.totals().total_segments[i].value;
                    }
                }
            },
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            }
        });
    }
);
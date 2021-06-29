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
        var foomanSurchargeConfig = window.checkoutConfig.foomanSurchargeConfig;
        return Component.extend({
            defaults: {
                foomanSurchargeConfig: foomanSurchargeConfig,
                template: 'Fooman_Surcharge/summary/surcharge'
            },
            totals: quote.getTotals(),

            getPureValue: function () {
                for (var i in this.totals().total_segments) {
                    if (this.totals().total_segments[i].code === 'fooman_surcharge') {
                        return this.totals().total_segments[i].value;
                    }
                }
            },
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            },
            getAsCurrency: function (value) {
                return this.getFormattedPrice(value);
            },
            isDisplayedTaxInclusive: function () {
                return foomanSurchargeConfig.isDisplayedTaxInclusive;
            },
            isDisplayedTaxExclusive: function () {
                return foomanSurchargeConfig.isDisplayedTaxExclusive;
            },
            isDisplayedBoth: function () {
                return foomanSurchargeConfig.isDisplayedBoth;
            },
            isDisplayed: function (value) {
                return (value != 0 && value != null) || foomanSurchargeConfig.isZeroDisplayed;
            },
            getSurcharges: function () {
                if (this.totals()) {
                    for (var i in this.totals().total_segments) {
                        if (this.totals().total_segments[i].code === 'fooman_surcharge') {
                            if (this.totals().total_segments[i].extension_attributes) {
                                return this.totals().total_segments[i].extension_attributes.fooman_surcharge_details.items;
                            }
                        }
                    }
                }
                return [];
            }
        });
    }
);
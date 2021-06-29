/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'mage/utils/wrapper',
    'mage/translate'
], function (wrapper, $t) {
    'use strict';
    return function (stepNavigator) {
        stepNavigator.registerStep = wrapper.wrapSuper(stepNavigator.registerStep, function (code, alias, title, isVisible, navigate, sortOrder) {
            if(code == 'shipping'){
                title = $t('Shipping details');
            }
            if(code == 'payment'){
                title = $t('Payment details');
            }
            this._super(code, alias, title, isVisible, navigate, sortOrder);
        });
        return stepNavigator;
    }
});

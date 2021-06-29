/*
 * @copyright Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define(function () {
    'use strict';
    return function (checkoutData, newValue) {
        return newValue && newValue.countryId;
    };
});
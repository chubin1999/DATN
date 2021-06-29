<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Plugin;

use Fooman\Surcharge\Model\AdjustNextTaxCalc;

class TaxConfig
{
    private $adjustNextTaxCalc;

    public function __construct(
        AdjustNextTaxCalc $adjustNextTaxCalc
    ) {
        $this->adjustNextTaxCalc = $adjustNextTaxCalc;
    }

    public function afterApplyTaxAfterDiscount(
        \Magento\Tax\Model\Config $subject,
        $result
    ) {
        if ($this->adjustNextTaxCalc->isAdjustmentNeeded()) {
            return false;
        }
        return $result;
    }
}

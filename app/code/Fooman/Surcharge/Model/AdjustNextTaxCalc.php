<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model;

class AdjustNextTaxCalc
{
    private $adjust;

    public function startAdjusting()
    {
        $this->adjust = true;
    }

    public function stopAdjusting()
    {
        $this->adjust = false;
    }

    public function isAdjustmentNeeded()
    {
        return $this->adjust;
    }
}

<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Model\ResourceModel;

class InvoiceTotal extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore -- Magento Core Use
    protected function _construct()
    {
        $this->_init('fooman_totals_invoice', 'entity_id');
    }
}

<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model\ResourceModel\Surcharge;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    //phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore -- Magento Core Use
    protected $_idFieldName = 'id';

    //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore -- Magento Core Use
    protected function _construct()
    {
        $this->_init(\Fooman\Surcharge\Model\Surcharge::class, \Fooman\Surcharge\Model\ResourceModel\Surcharge::class);
    }
}

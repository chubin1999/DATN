<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Api;

use Fooman\Surcharge\Api\Data\TypeInterface;
use Fooman\Totals\Api\Data\QuoteAddressTotalInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Api\Data\CartInterface;

interface SurchargeInterface
{

    /**
     * @param CartInterface    $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     *
     * @return QuoteAddressTotalInterface[]
     */
    public function collect(
        CartInterface $quote,
        ShippingAssignmentInterface $shippingAssignment
    );

    /**
     * @return TypeInterface
     */
    public function getTypeInstance();

    /**
     * @return mixed
     */
    public function getTypeId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getDescription();
}

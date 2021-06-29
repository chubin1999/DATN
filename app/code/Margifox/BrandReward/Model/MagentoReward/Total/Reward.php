<?php

namespace Margifox\BrandReward\Model\MagentoReward\Total;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address;

class Reward extends \Magento\Reward\Model\Total\Quote\Reward
{
    /**
     * Disable functionality
     */
    public function collect(\Magento\Quote\Model\Quote $quote, ShippingAssignmentInterface $shippingAssignment, Address\Total $total)
    {
        return $this;
    }
}

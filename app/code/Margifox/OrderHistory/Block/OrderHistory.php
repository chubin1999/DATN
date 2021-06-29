<?php

namespace Margifox\OrderHistory\Block;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Widget\Block\BlockInterface;

/**
 * Class OrderHistory
 *
 * @package Margifox\OrderHistory\Block
 */
class OrderHistory extends Template implements BlockInterface
{
    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var SessionFactory
     */
    protected $_customerSession;

    /**
     * OrderHistory constructor.
     * @param Context $context
     * @param array $data
     * @param OrderFactory $orderFactory
     * @param SessionFactory $customerSession
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        SessionFactory $customerSession,
        array $data = []
    )
    {
        $this->_orderFactory = $orderFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Get order history by customer id
     * @return false|\Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getOrderHistory()
    {
        $limit = $this->getData('limit') ? $this->getData('limit') : 5;
        $customerId = $this->_customerSession->create()->getId();
        if ($customerId) {
            $orders = $this->_orderFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('created_at', 'desc')
                ->setPageSize($limit);
            return $orders;
        }
        return false;
    }

    /**
     * @param $order
     * @return string
     */
    public function getOrderUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * Get reorder URL
     *
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }
}

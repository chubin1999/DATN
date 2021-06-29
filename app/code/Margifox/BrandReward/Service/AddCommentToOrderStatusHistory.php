<?php

namespace Margifox\BrandReward\Service;

class AddCommentToOrderStatusHistory
{
    /**
     * @var \Magento\Sales\Model\Order\Status\HistoryFactory
     */
    private $orderHistoryFactory;

    /**
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory
     */
    public function __construct(
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory
    ) {
        $this->orderHistoryFactory = $orderHistoryFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string $comment
     * @param string $entityType
     * @param bool $isVisibleOnFront
     *
     * @return \Magento\Sales\Model\Order
     */
    public function execute($order, $comment, $entityType, $isVisibleOnFront = null)
    {
        if ($isVisibleOnFront === null) {
            // TODO: Default status per $entityType?
            $isVisibleOnFront = false;
        }

        $history = $this->orderHistoryFactory->create()
            ->setStatus($order->getStatus())
            ->setComment($comment)
            ->setEntityName($entityType)
            ->setIsVisibleOnFront($isVisibleOnFront);
        $order->addStatusHistory($history);
        $order->save();

        return $order;
    }
}

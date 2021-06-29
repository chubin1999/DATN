<?php


namespace Margifox\OrderHistory\Controller\Orders;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Sales\Helper\Reorder;
use Margifox\OrderHistory\Block\OrderHistory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var OrderHistory
     */
    private $orderHistory;
    /**
     * @var Reorder
     */
    private $reorder;
    /**
     * @var PostHelper
     */
    private $postHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param OrderHistory $orderHistory
     * @param Reorder $reorder
     * @param PostHelper $postHelper
     */
    public function __construct(
        Context $context,
        OrderHistory $orderHistory,
        Reorder $reorder,
        PostHelper $postHelper
    )
    {
        $this->orderHistory = $orderHistory;
        $this->reorder = $reorder;
        $this->postHelper = $postHelper;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orders = [];
        if ($this->orderHistory->getOrderHistory()) {
            $collection = $this->orderHistory->getOrderHistory();
            foreach ($collection as $item) {
                $order = [
                    'url' => $this->orderHistory->getOrderUrl($item),
                    'increment_id' => $item->getIncrementId(),
                    'grand_total' => $item->formatPrice($item->getGrandTotal()),
                    'status' => $item->getStatus(),
                    'reorder' => $this->reorder->canReorder($item->getEntityId()) ?
                        $this->postHelper->getPostData($this->orderHistory->getReorderUrl($item)) : '',
                ];
                $orders[] = $order;
            }
        }
        return $this->getResponse()->setBody(json_encode($orders));
    }
}

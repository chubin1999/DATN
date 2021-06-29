<?php

namespace Margifox\BrandRewardUi\Block;

class RewardPoints extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $orders;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory
     */
    protected $orderHistoryCollectionFactory;

    /**
     * @var \Margifox\Brand\Api\BrandRepositoryInterface
     */
    private $brandRepository;

    /**
     * @var array
     */
    protected $brands;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\Collection
     */
    protected $ordersHistory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $orderHistoryCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Margifox\Brand\Api\BrandRepositoryInterface $brandRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $orderHistoryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Margifox\Brand\Api\BrandRepositoryInterface $brandRepository,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->orderHistoryCollectionFactory = $orderHistoryCollectionFactory;
        $this->brandRepository = $brandRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Status\History\Collection|bool
     */
    public function getOrdersHistory()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->ordersHistory) {
            $collection = $this->orderHistoryCollectionFactory->create();
            $collection
                ->addFieldToFilter('entity_name', ['in' => $this->getFilters()])
                ->join(
                'sales_order',
                'main_table.parent_id=sales_order.entity_id AND sales_order.customer_id = ' . $customerId,
                ''
            );

            $this->ordersHistory = $collection->setOrder('created_at','desc');
        }

        return $this->ordersHistory;
    }

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\Collection $ordersHistory
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders($ordersHistory)
    {
        if (!$this->orders) {
            $orderIds = array_unique($ordersHistory->getColumnValues('parent_id'));

            $orderCollection = $this->orderCollectionFactory->create();
            $orders = $orderCollection->addFieldToFilter('entity_id', ['in' => $orderIds])
                ->setOrder('created_at','desc')
                ->getItems();

            foreach ($orders as $order) {
                $this->orders[$order->getEntityId()] = $order;
            }
        }

        return $this->orders;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getEmptyOrdersMessage()
    {
        return __('You have placed no orders.');
    }

    /**
     * @return $this|\Magento\Framework\View\Element\Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrdersHistory()) {
            $pager = $this->getLayout()
                ->createBlock(\Magento\Theme\Block\Html\Pager::class, 'sales.order.history.pager')
                ->setCollection($this->getOrdersHistory());
            $this->setChild('pager', $pager);
            $this->getOrdersHistory()->load();
        }

        return $this;
    }

    /**
     * @param int $brandId
     * @return mixed|string
     */
    public function getBrandName($brandId)
    {
        $comment = json_decode($brandId, true);
        $brandId = $comment['brandId'];
        $brands = $this->getBrands();

        return $brands[$brandId] ?? '';
    }

    /**
     * @param int $orderId
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder($orderId)
    {
        return $this->orders[$orderId];
    }

    /**
     * @param string $comment
     * @return string
     */
    public function getPoints($comment)
    {
        $comment = json_decode($comment, true);
        if (isset($comment['points_earned'])) {
            return '+' . $comment['points_earned'];
        }

        if (isset($comment['amount_redeemed'])) {
            return '-' . $comment['amount_redeemed'];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->_data['filters'];
    }

    /**
     * @return array
     */
    protected function getBrands()
    {
        if ($this->brands) {
            return $this->brands;
        }

        $brands = $this->brandRepository->getAll();
        foreach ($brands as $brand) {
            $this->brands[$brand->getId()] = $brand->getName();
        }

        return $this->brands;
    }
}

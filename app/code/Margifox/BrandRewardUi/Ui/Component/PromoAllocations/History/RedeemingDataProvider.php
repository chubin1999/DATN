<?php

namespace Margifox\BrandRewardUi\Ui\Component\PromoAllocations\History;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory as HistoryStatusCollection;
use Margifox\Brand\Api\BrandRepositoryInterface;
use Margifox\BrandReward\Model\Source\Order\StatusHistoryEntityType;

class RedeemingDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var HistoryStatusCollection
     */
    private $orderHistoryCollectionFactory;

    /**
     * @var \Magento\Reward\Model\Reward\Rate
     */
    private $rate;

    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;

    /**
     * @param OrderRepository $orderRepository
     * @param HistoryStatusCollection $orderHistoryCollectionFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Reward\Model\Reward\Rate $rate
     * @param BrandRepositoryInterface $brandRepository
     * @param RequestInterface $request
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        OrderRepository $orderRepository,
        HistoryStatusCollection $orderHistoryCollectionFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Reward\Model\Reward\Rate $rate,
        BrandRepositoryInterface $brandRepository,
        RequestInterface $request,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->json = $json;
        $this->rate = $rate;
        $this->brandRepository = $brandRepository;
        $this->orderHistoryCollectionFactory = $orderHistoryCollectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Status\History\Collection
     */
    public function getCollection()
    {
        $collection = parent::getCollection();
        if ($collection === null) {
            $collection = $this->orderHistoryCollectionFactory->create();
        }
        $companyId = $this->request->getParam('company_id') ?? null;
        $collection = $collection->addFieldToFilter('entity_name', StatusHistoryEntityType::PROMO_ALLOCATION_REDEEMING);
        if (!$companyId) {
            return $collection;
        }

        $collection = $collection->join(
            'company_order_entity',
            'main_table.parent_id=company_order_entity.order_id AND company_order_entity.company_id = ' . $companyId
        );

        return $collection;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        $data = parent::getData();

        /** @var array $historyItem */
        foreach ($data['items'] as &$historyItem) {
            // TODO: performance improvements - load all orders at once

            // TODO: add logic for credit_memo
            $order = $this->orderRepository->get($historyItem['parent_id']);
            $historyRecord = $historyItem['comment'];
            if (!$historyRecord) {
                continue;
            }

            $historyRecord = $this->json->unserialize($historyRecord);
            $historyItem = array_merge($historyItem, $historyRecord);
            $pointsEarned = $this->rate->calculateToCurrency($historyRecord['amount_redeemed']);
            $historyItem['amount_redeemed_dollar'] = $pointsEarned;
            $historyItem['order_number'] = $order->getIncrementId();
            $brand = $this->brandRepository->getById($historyRecord['brandId']);
            $historyItem['brand'] = $brand->getName();
        }

        return $data;
    }
}

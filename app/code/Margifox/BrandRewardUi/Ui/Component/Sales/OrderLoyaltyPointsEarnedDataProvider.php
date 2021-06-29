<?php

namespace Margifox\BrandRewardUi\Ui\Component\Sales;

use Magento\Framework\App\RequestInterface;
use Margifox\BrandReward\Model\Source\Order\StatusHistoryEntityType;

class OrderLoyaltyPointsEarnedDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory
     */
    private $orderHistoryCollectionFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * @var \Margifox\Brand\Api\BrandRepositoryInterface
     */
    private $brandRepository;

    /**
     * @param \Margifox\Brand\Api\BrandRepositoryInterface $brandRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $orderHistoryCollectionFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param RequestInterface $request
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Margifox\Brand\Api\BrandRepositoryInterface $brandRepository,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $orderHistoryCollectionFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        RequestInterface $request,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->brandRepository = $brandRepository;
        $this->request = $request;
        $this->json = $json;
        $this->orderHistoryCollectionFactory = $orderHistoryCollectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|\Magento\Sales\Model\ResourceModel\Order\Status\History\Collection
     */
    public function getCollection()
    {
        $collection = parent::getCollection();
        if ($collection === null) {
            $collection = $this->orderHistoryCollectionFactory->create();
        }

        $orderId = $this->request->getParam('order_id') ?? null;
        $collection->addFieldToFilter('entity_name', StatusHistoryEntityType::LOYALTY_EARNING)
            ->addFieldToFilter('parent_id', $orderId);

        return $collection;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();

        /** @var array $historyItem */
        foreach ($data['items'] as &$historyItem) {
            if (!$historyItem['comment']) {
                continue;
            }
            $historyRecord = $this->json->unserialize($historyItem['comment']);
            $historyItem = array_merge($historyItem, $historyRecord);
            $brand = $this->brandRepository->getById($historyItem['brandId']);
            $historyItem['brand'] = $brand->getName();
        }

        return $data;
    }
}

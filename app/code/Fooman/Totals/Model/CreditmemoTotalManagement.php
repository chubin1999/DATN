<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Model;

use Fooman\Totals\Model\ResourceModel\CreditmemoTotal\CollectionFactory;

class CreditmemoTotalManagement
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param  int $creditmemoId
     *
     * @return \Fooman\Totals\Api\Data\CreditmemoTotalInterface[]
     */
    public function getByCreditmemoId($creditmemoId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\Collection $creditmemoTotalCollection */
        $creditmemoTotalCollection = $this->collectionFactory->create();

        $collection = $creditmemoTotalCollection
            ->addFieldToFilter('creditmemo_id', (string) $creditmemoId);

        return $collection->getItems();
    }

    /**
     * @param string $typeId
     * @param int $creditmemoId
     *
     * @return \Fooman\Totals\Api\Data\CreditmemoTotalInterface[]
     */
    public function getByTypeAndCreditmemoId($typeId, $creditmemoId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\Collection $creditmemoTotalCollection */
        $creditmemoTotalCollection = $this->collectionFactory->create();

        $collection = $creditmemoTotalCollection
            ->addFieldToFilter('type_id', $typeId)
            ->addFieldToFilter('creditmemo_id', (string) $creditmemoId);

        return $collection->getItems();
    }

    /**
     * @param  string    $code
     * @param  int       $creditmemoId
     *
     * @return \Fooman\Totals\Api\Data\CreditmemoTotalInterface[]
     */
    public function getByCodeAndCreditmemoId($code, $creditmemoId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\Collection $creditmemoTotalCollection */
        $creditmemoTotalCollection = $this->collectionFactory->create();

        $collection = $creditmemoTotalCollection
            ->addFieldToFilter('code', $code)
            ->addFieldToFilter('creditmemo_id', (string) $creditmemoId);

        return $collection->getItems();
    }

    /**
     * @param  int $orderId
     *
     * @return \Fooman\Totals\Api\Data\CreditmemoTotalInterface[]
     */
    public function getByOrderId($orderId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\Collection $creditmemoTotalCollection */
        $creditmemoTotalCollection = $this->collectionFactory->create();

        $collection = $creditmemoTotalCollection
            ->addFieldToFilter('order_id', (string) $orderId);

        return $collection->getItems();
    }

    /**
     * @param  string $code
     * @param  int    $orderId
     *
     * @return \Fooman\Totals\Api\Data\CreditmemoTotalInterface[]
     */
    public function getByCodeAndOrderId($code, $orderId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\Collection $creditmemoTotalCollection */
        $creditmemoTotalCollection = $this->collectionFactory->create();

        $collection = $creditmemoTotalCollection
            ->addFieldToFilter('code', $code)
            ->addFieldToFilter('order_id', (string) $orderId);

        return $collection->getItems();
    }
}

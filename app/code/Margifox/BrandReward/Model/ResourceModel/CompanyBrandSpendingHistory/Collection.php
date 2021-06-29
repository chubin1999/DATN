<?php

namespace Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'history_id';

    /**
     * @var \Margifox\BrandReward\Provider\GeneralConfiguration
     */
    protected $generalConfiguration;

    /**
     * @param \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfiguration
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfiguration,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->generalConfiguration = $generalConfiguration;
    }

    protected function _construct()
    {
        $this->_init(
            \Margifox\BrandReward\Model\CompanyBrandSpendingHistory::class,
            \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingHistory::class
        );
    }

    /**
     * @param int $days
     * @return $this
     * @throws \Exception
     */
    public function loadExpiredSoonPoints($days)
    {
        $lifetimeInDays = $this->generalConfiguration->getLifetime();
        if (!$lifetimeInDays) {
            return $this;
        }

        $expireAtLimit = (new \DateTime())
            ->sub(new \DateInterval('P' . $days . 'D'))
            ->format('Y-m-d H:i:s');
        $this->getSelect()->columns(['total_expired' => new \Zend_Db_Expr('SUM(points_delta-points_used)')])
            ->where('points_delta-points_used > 0')
            ->where('expiry_notification_sent = 0')
            ->where('status=1')
            ->where('created_at < ?', $expireAtLimit)
            ->group(['main_table.company_id', 'main_table.brand_id']);

        $this->setFlag('expired_soon_points_loaded', true);

        return $this;
    }

    /**
     * Return array of history ids records that will be expired.
     * Required loadExpiredSoonPoints() call first, based on its select object
     *
     * @return array|bool
     */
    public function getExpiredSoonIds()
    {
        if (!$this->getFlag('expired_soon_points_loaded')) {
            return [];
        }

        $additionalWhere = [];
        foreach ($this as $item) {
            $where = [
                $this->getConnection()->quoteInto('main_table.company_id=?', $item->getCompanyId())
            ];
            $additionalWhere[] = '(' . implode(' AND ', $where) . ')';
        }
        if (count($additionalWhere) == 0) {
            return [];
        }
        // filter rows by customer and store, as result of grouped query
        $where = new \Zend_Db_Expr(implode(' OR ', $additionalWhere));

        $select = clone $this->getSelect();
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns('history_id')
            ->reset(\Magento\Framework\DB\Select::GROUP)
            ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
            ->where($where);

        return $this->getConnection()->fetchCol($select);
    }
}

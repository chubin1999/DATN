<?php

namespace Margifox\BrandReward\Model\ResourceModel;

use Margifox\BrandReward\Model\Source\SpendingHistory\HistoryStatus;
use Margifox\BrandReward\Model\Source\SpendingHistory\RewardType;

class CompanyBrandSpendingHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    protected function _construct()
    {
        $this->_init('company_brand_spending_history', 'history_id');
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        ?string $connectionName = null
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($context, $connectionName);
    }

    /**
     * Retrieve actual history records that have unused points, i.e. points_delta-points_used > 0
     * Update points_used field for non-used points
     *
     * @param \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface $history
     * @param int $required Points total that required
     * @return $this
     * @throws \Exception
     */
    public function useAvailableLoyaltyPoints($history, $required)
    {
        $required = (int)abs($required);
        if (!$required) {
            return $this;
        }

        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();
            $select = $connection->select()
                ->from(
                ['history' => $this->getMainTable()],
                ['history_id', 'points_delta', 'points_used']
            )
                ->where('company_id = ?', $history->getCompanyId())
                ->where('brand_id = ?', $history->getBrandId())
                ->where('status IN (?)', [HistoryStatus::ACTIVE, HistoryStatus::REDEEMING])
                ->where('reward_type = ?', RewardType::LOYALTY)
                ->where('points_delta - points_used > 0')
                ->order('history_id')
                ->forUpdate(true);
            $stmt = $connection->query($select);

            $data = [];
            while ($row = $stmt->fetch()) {
                if ($required <= 0) {
                    break;
                }
                $rowAvailable = $row['points_delta'] - $row['points_used'];
                $pointsUsed = min($required, $rowAvailable);
                $required -= $pointsUsed;
                $newPointsUsed = $pointsUsed + $row['points_used'];
                $data[] = ['history_id' => $row['history_id'], 'points_used' => $newPointsUsed];
            }

            if (count($data) > 0) {
                $connection->insertOnDuplicate($this->getMainTable(), $data, ['history_id', 'points_used']);
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * @param array $ids
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function markAsNotified($ids)
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            ['expiry_notification_sent' => 1],
            ['history_id IN (?)' => $ids]
        );

        return $this;
    }

    public function expirePoints($expiryDate, $limit)
    {
        $connection = $this->getConnection();
        $expireAtLimit = (new \DateTime())
            ->sub(new \DateInterval('P' . $expiryDate . 'D'))
            ->format('Y-m-d H:i:s');

        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('created_at < :time_now')
            ->where('status <> ?',2)
            ->where('points_delta-points_used > ?',0)
            ->limit((int)$limit);
        $bind = [':time_now' => $expireAtLimit];
        $duplicates = [];
        $expiredAmounts = [];
        $expiredHistoryIds = [];
        $stmt = $connection->query($select, $bind);

        while ($row = $stmt->fetch()) {
            $row['status'] = '2';
            $expiredHistoryIds[] = $row['history_id'];
            unset($row['history_id']);
            if (!isset($expiredAmounts[$row['company_id']][$row['brand_id']][$row['reward_type']])) {
                $expiredAmounts[$row['company_id']][$row['brand_id']][$row['reward_type']] = 0;
            }
            $expiredAmount = $row['points_delta'] - $row['points_used'];
            $expiredAmounts[$row['company_id']][$row['brand_id']][$row['reward_type']] += $expiredAmount;
            $duplicates[] = $row;
        }

        $mapper = [
            \Margifox\BrandReward\Model\Source\SpendingHistory\RewardType::LOYALTY => 'loyalty_points_balance',
            \Margifox\BrandReward\Model\Source\SpendingHistory\RewardType::PROMO_ALLOCATION => 'allocation_points_balance'
        ];

        if (count($expiredHistoryIds) > 0) {
            foreach ($expiredAmounts as $companyId => $expired) {
                foreach ($expired as $brandId => $rewardType) {
                    foreach ($rewardType as $type => $amount) {
                        $key = $mapper[$type];
                        if ($amount == 0) {
                            continue;
                        }
                        $bind = [
                            $key => $connection->getCheckSql(
                                "{$key} > {$amount}",
                                "{$key}-{$amount}",
                                0
                            ),
                        ];
                        $where = [
                            'company_id=?' => $companyId,
                            'brand_id=?' => $brandId
                        ];
                        $connection->update($this->getTable('company_brand_spending_level'), $bind, $where);
                    }
                }
            }

            // duplicate expired records
            $connection->insertMultiple($this->getMainTable(), $duplicates);

            // update is_expired field (using history ids instead where clause for better performance)
            $connection->update(
                $this->getMainTable(),
                ['status' => '2'],
                ['history_id IN (?)' => $expiredHistoryIds]
            );
        }

        return $this;
    }
}

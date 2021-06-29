<?php

namespace Margifox\BrandReward\Service;

use Margifox\BrandReward\Model\Source\SpendingHistory\ActionType;
use Margifox\BrandReward\Model\Source\SpendingHistory\HistoryStatus;
use Margifox\BrandReward\Model\Source\SpendingHistory\RewardType;

class EarnRewardsOnOrderCreate
{
    /**
     * @var CalculateLoyaltyPoints
     */
    protected $calculateLoyaltyPoints;

    /**
     * @var CalculatePromoAllocation
     */
    protected $calculatePromoAllocation;

    /**
     * @var \Margifox\Brand\Model\Brand\Repository
     */
    protected $brandRepository;

    /**
     * @var \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface
     */
    protected $companyBrandSpendingLevel;

    /**
     * @var \Margifox\BrandReward\Service\AddCommentToOrderStatusHistory
     */
    protected $addCommentToOrderStatusHistory;

    /**
     * @var \Margifox\BrandReward\Api\CompanyBrandSpendingHistoryRepositoryInterface
     */
    protected $companyBrandSpendingHistoryRepository;

    /**
     * @var \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterfaceFactory
     */
    protected $companyBrandSpendingHistoryFactory;

    /**
     * @var \Margifox\BrandReward\Service\GetCurrentCustomerCompany
     */
    protected $currentCustomerCompany;

    /**
     * @var \Magento\Reward\Model\SalesRule\RewardPointCounter
     */
    protected $rewardPointCounter;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    public function __construct(
        CalculateLoyaltyPoints $calculateLoyaltyPoints,
        CalculatePromoAllocation $calculatePromoAllocation,
        \Margifox\Brand\Model\Brand\Repository $brandRepository,
        \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevel,
        \Margifox\BrandReward\Service\AddCommentToOrderStatusHistory $addCommentToOrderStatusHistory,
        \Margifox\BrandReward\Api\CompanyBrandSpendingHistoryRepositoryInterface $companyBrandSpendingHistoryRepository,
        \Margifox\BrandReward\Service\GetCurrentCustomerCompany $currentCustomerCompany,
        \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterfaceFactory $companyBrandSpendingHistoryInterfaceFactory,
        \Magento\Reward\Model\SalesRule\RewardPointCounter $rewardPointCounter,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->calculateLoyaltyPoints = $calculateLoyaltyPoints;
        $this->calculatePromoAllocation = $calculatePromoAllocation;
        $this->brandRepository = $brandRepository;
        $this->companyBrandSpendingLevel = $companyBrandSpendingLevel;
        $this->currentCustomerCompany = $currentCustomerCompany;
        $this->addCommentToOrderStatusHistory = $addCommentToOrderStatusHistory;
        $this->companyBrandSpendingHistoryFactory = $companyBrandSpendingHistoryInterfaceFactory;
        $this->companyBrandSpendingHistoryRepository = $companyBrandSpendingHistoryRepository;
        $this->rewardPointCounter = $rewardPointCounter;
        $this->eventManager = $eventManager;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Sales\Model\Order $order)
    {
        $companyId = $this->currentCustomerCompany->execute();
        if (!$companyId) {
            return $this;
        }

        $productBrands = [];
        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productBrands[$product->getData('brand')][] = $item;
        }

        // Get Spending levels for company
        $companySpendingLevels = $this->companyBrandSpendingLevel->getByCompany($companyId);
        if (!$companySpendingLevels) {
            return $this;
        }

        $brands = $this->brandRepository->getAll();
        $infoPerBrand = [];
        foreach ($brands as $brand) {
            // Link brand->id and brandOption->id
            foreach ($productBrands as $brandId => $items) {
                if ($brandId == $brand->getOptionLinkId()) {
                    foreach ($items as $orderItem) {
                        $infoPerBrand[$brand->getId()][] = $orderItem;
                    }
                }
            }
        }

        $spendingHistory = [];
        foreach ($companySpendingLevels as $companySpendingLevel) {
            $brandId = $companySpendingLevel->getBrandId();
            if (isset($infoPerBrand[$brandId])) {
                $itemsPrice = 0;
                /** @var \Magento\Sales\Model\Order\Item $orderItem */
                foreach ($infoPerBrand[$brandId] as $orderItem) {
                    $itemsPrice += $orderItem->getRowTotalInclTax();
                }

                $loyaltyPointsEarned = $this->calculateLoyaltyPoints->execute($itemsPrice, $companySpendingLevel);
                $rewardsAllocationAmount = $this->calculatePromoAllocation->execute($itemsPrice, $companySpendingLevel);
                $spendingHistory[$brandId]['OrigTransactionAmount'] = $itemsPrice;
                $spendingHistory[$brandId]['LoyaltyPointsEarned'] = $loyaltyPointsEarned;
                $spendingHistory[$brandId]['RewardsAllocationAmount'] = $rewardsAllocationAmount;
                $spendingHistory[$brandId]['NewBalancePoints'] = $companySpendingLevel->getLoyaltyPointsBalance() + $loyaltyPointsEarned;
                $spendingHistory[$brandId]['NewBalancePromoAllocation'] = $companySpendingLevel->getAllocationPointsBalance() + $rewardsAllocationAmount;
            }
        }

        foreach ($spendingHistory as $brandId => $data) {
            /** @var \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterface $brandSpendingHistory */
            $brandSpendingHistory = $this->companyBrandSpendingHistoryFactory->create();
            $brandSpendingHistory
                ->setRewardType(RewardType::LOYALTY)
                ->setStatus(HistoryStatus::ACTIVE)
                ->setCompanyId($companyId)
                ->setBrandId($brandId)
                ->setOrigTransactionAmount($data['OrigTransactionAmount'])
                ->setLoyaltyPointsEarned($data['LoyaltyPointsEarned'])
                ->setRewardsAllocationAmount($data['RewardsAllocationAmount'])
                ->setActionType(ActionType::PURCHASE)
                ->setOrderId($order->getId())
                ->setPointsDelta($data['LoyaltyPointsEarned'])
                ->setNewBalancePoints($data['NewBalancePoints'])
                ->setNewBalancePromoAllocation($data['NewBalancePromoAllocation'])
                ->setExpiryNotificationSent(0);

            $this->companyBrandSpendingHistoryRepository->save($brandSpendingHistory);

            if ($data['LoyaltyPointsEarned'] > 0) {
                $this->addCommentToOrderStatusHistory->execute(
                    $order,
                    json_encode([
                        'message' => __('The customer earned loyalty points %1 for this order.', $data['LoyaltyPointsEarned']),
                        'brandId' => $brandId,
                        'points_earned' => $data['LoyaltyPointsEarned']
                    ]),
                    \Margifox\BrandReward\Model\Source\Order\StatusHistoryEntityType::LOYALTY_EARNING
                );
            }
            if ($data['RewardsAllocationAmount']) {
                $this->addCommentToOrderStatusHistory->execute(
                    $order,
                    json_encode([
                        'message' => __('The customer earned %1 promo allocations dollars for this order.', $data['RewardsAllocationAmount']),
                        'brandId' => $brandId,
                        'points_earned' => $data['RewardsAllocationAmount']
                    ]),
                    \Margifox\BrandReward\Model\Source\Order\StatusHistoryEntityType::PROMO_ALLOCATION_EARNING
                );
            }

            $this->eventManager->dispatch(
                'recalculate_company_spending_level',
                [
                    'companyId' => $companyId,
                    'history' => $brandSpendingHistory
                ]
            );
        }

        return $this;
    }
}

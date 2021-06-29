<?php

namespace Margifox\BrandReward\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Margifox\BrandReward\Model\Source\SpendingHistory\HistoryStatus;
use Margifox\BrandReward\Model\Source\SpendingHistory\RewardType;
use Margifox\BrandReward\Model\Source\SpendingHistory\ActionType;

class RedeemForOrder implements ObserverInterface
{
    protected $companyBalanceValidator;
    protected $rewardHelper;
    protected $generalConfig;
    protected $companyBrandSpendingHistoryFactory;
    protected $currentCustomerCompany;
    protected $brandRepository;
    protected $companyBrandSpendingHistoryRepository;
    protected $addCommentToOrderStatusHistory;
    protected $eventManager;
    protected $companyBrandSpendingLevel;

    public function __construct(
        \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfig,
        \Margifox\Brand\Model\Brand\Repository $brandRepository,
        \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevel,
        \Margifox\BrandReward\Service\AddCommentToOrderStatusHistory $addCommentToOrderStatusHistory,
        \Margifox\BrandReward\Api\CompanyBrandSpendingHistoryRepositoryInterface $companyBrandSpendingHistoryRepository,
        \Margifox\BrandReward\Service\GetCurrentCustomerCompany $currentCustomerCompany,
        \Margifox\BrandReward\Api\Data\CompanyBrandSpendingHistoryInterfaceFactory $companyBrandSpendingHistoryInterfaceFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Margifox\BrandReward\Model\Validator\CompanyBalance $companyBalanceValidator
    ) {
        $this->generalConfig = $generalConfig;
        $this->brandRepository = $brandRepository;
        $this->companyBrandSpendingLevel = $companyBrandSpendingLevel;
        $this->currentCustomerCompany = $currentCustomerCompany;
        $this->addCommentToOrderStatusHistory = $addCommentToOrderStatusHistory;
        $this->companyBrandSpendingHistoryFactory = $companyBrandSpendingHistoryInterfaceFactory;
        $this->companyBrandSpendingHistoryRepository = $companyBrandSpendingHistoryRepository;
        $this->eventManager = $eventManager;
        $this->companyBalanceValidator = $companyBalanceValidator;
    }

    public function execute(Observer $observer)
    {
        if (!$this->generalConfig->isEnabled()) {
            return;
        }

        $event = $observer->getEvent();

        /* @var \Magento\Sales\Model\Order */
        $order = $event->getOrder();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $event->getQuote();

        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $event->getAddress();

        if ($quote->getBaseRewardCurrencyAmount() <= 0) {
            return;
        }

        $this->companyBalanceValidator->validate($quote);
        $rewardData = $quote->getIsMultiShipping() ? $address : $quote;

        $pointsDelta = $rewardData->getRewardPointsBalance();

        $order->setRewardPointsBalance(round($rewardData->getRewardPointsBalance()));
        $order->setRewardCurrencyAmount($rewardData->getRewardCurrencyAmount());
        $order->setBaseRewardCurrencyAmount($rewardData->getBaseRewardCurrencyAmount());

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

                $spendingHistory[$brandId]['OrigTransactionAmount'] = $itemsPrice;
                $spendingHistory[$brandId]['LoyaltyPointsEarned'] = $this->calculateLoyaltyPoints($order);
                $spendingHistory[$brandId]['RewardsAllocationAmount'] = $this->calculatePromoAllocation($order);
                $spendingHistory[$brandId]['NewBalancePoints'] = $companySpendingLevel->getLoyaltyPointsBalance() - $pointsDelta;
                $spendingHistory[$brandId]['NewBalancePromoAllocation'] = $companySpendingLevel->getAllocationPointsBalance() - $pointsDelta;
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
                ->setLoyaltyPointsEarned(0)
                ->setRewardsAllocationAmount(0)
                ->setActionType(ActionType::REDEEM)
                ->setOrderId(null)
                ->setIncrementId($quote->getReservedOrderId())
                ->setPointsDelta(-$pointsDelta)
                ->setNewBalancePoints($data['NewBalancePoints'])
                ->setNewBalancePromoAllocation($data['NewBalancePromoAllocation'])
                ->setExpiryNotificationSent(0);

            $this->companyBrandSpendingHistoryRepository->save($brandSpendingHistory);
            $this->addCommentToOrderStatusHistory->execute(
                $order,
                json_encode([
                    'message' => __('The customer redeemed %1 for this order.', $pointsDelta),
                    'brandId' => $brandId,
                    'amount_redeemed' => $pointsDelta
                ]),
                \Margifox\BrandReward\Model\Source\Order\StatusHistoryEntityType::LOYALTY_REDEEMING
            );
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

    private function calculatePromoAllocation($quote)
    {
        return $quote->getBaseRewardCurrencyAmount();
    }

    private function calculateLoyaltyPoints($quote)
    {
        return $quote->getBaseRewardCurrencyAmount();
    }
}

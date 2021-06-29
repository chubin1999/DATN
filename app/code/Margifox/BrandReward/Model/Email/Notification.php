<?php

namespace Margifox\BrandReward\Model\Email;

class Notification
{
    /**
     * @var \Margifox\BrandReward\Provider\NotificationsConfiguration
     */
    protected $notificationsConfiguration;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Company\Api\CompanyManagementInterface
     */
    protected $companyManagement;

    /**
     * @param \Margifox\BrandReward\Provider\NotificationsConfiguration $notificationsConfiguration
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Company\Api\CompanyManagementInterface $companyManagement
     */
    public function __construct(
        \Margifox\BrandReward\Provider\NotificationsConfiguration $notificationsConfiguration,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Company\Api\CompanyManagementInterface $companyManagement
    ) {
        $this->notificationsConfiguration = $notificationsConfiguration;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->companyManagement = $companyManagement;
    }

    /**
     * @param \Margifox\BrandReward\Model\CompanyBrandSpendingHistory $item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendPointsExpiredNotification($item)
    {
        if (!$this->notificationsConfiguration->isNotificationEnabled()) {
            return;
        }
        $templateId = $this->notificationsConfiguration->getExpiredNotificationTemplate();
        $from = $this->notificationsConfiguration->getNotificationSender();
        if (!$templateId || !$from) {
            return;
        }

        $admin = $this->companyManagement->getAdminByCompanyId($item->getCompanyId());

        $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setFrom($from)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => 0
                ]
            )->setTemplateVars(
                [
                    'customer_name' => $admin->getFirstname() . ' ' . $admin->getLastname(),
                    //'unsubscription_url' => $this->_rewardCustomer->getUnsubscribeUrl('warning'),
                    'remaining_days' => $this->notificationsConfiguration->getExpiryDayBefore(),
                    //'points_balance' => $item->getPointsBalanceTotal(),
                    'points_expiring' => $item->getTotalExpired(),
                    //'reward_amount_now' => $helper->formatAmount($amount, true, $item->getStoreId())      // TODO: convert and format
                ]
            )->addTo($admin->getEmail());
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
    }
}

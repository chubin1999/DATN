<?php

namespace Margifox\BrandReward\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Margifox\BrandReward\Provider\GeneralConfiguration
     */
    private $generalConfig;

    /**
     * @var \Margifox\BrandReward\Service\EarnRewardsOnOrderCreate
     */
    private $earnRewardsOnOrderCreate;

    /**
     * @param \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfig
     * @param \Margifox\BrandReward\Service\EarnRewardsOnOrderCreate $earnRewardsOnOrderCreate
     */
    public function __construct(
        \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfig,
        \Margifox\BrandReward\Service\EarnRewardsOnOrderCreate $earnRewardsOnOrderCreate
    ) {
        $this->generalConfig = $generalConfig;
        $this->earnRewardsOnOrderCreate = $earnRewardsOnOrderCreate;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->generalConfig->isEnabled()) {
            return $this;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $this->earnRewardsOnOrderCreate->execute($order);

        return $this;
    }
}

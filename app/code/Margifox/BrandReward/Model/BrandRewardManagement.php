<?php

namespace Margifox\BrandReward\Model;

use Margifox\BrandReward\Api\BrandRewardManagementInterface;

class BrandRewardManagement implements BrandRewardManagementInterface
{
    /**
     * @var \Margifox\BrandReward\Provider\GeneralConfiguration
     */
    protected $generalConfiguration;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Margifox\BrandReward\Model\PaymentDataImporter
     */
    protected $importer;

    /**
     * @param \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfiguration
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Margifox\BrandReward\Model\PaymentDataImporter $importer
     */
    public function __construct(
        \Margifox\BrandReward\Provider\GeneralConfiguration $generalConfiguration,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Margifox\BrandReward\Model\PaymentDataImporter $importer
    ) {
        $this->generalConfiguration = $generalConfiguration;
        $this->quoteRepository = $quoteRepository;
        $this->importer = $importer;
    }

    /**
     * @param int $cartId
     * @param int $brandId
     * @param string $rewardType
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function set($cartId, $brandId, $rewardType)
    {
        if (!$this->generalConfiguration->isEnabled()) {
            return false;
        }

        /* @var $quote \Magento\Quote\Model\Quote */
        $quote = $this->quoteRepository->getActive($cartId);

        $brandRewardIds = $quote->getData('brand_reward_ids');
        $brandRewardIds = $brandRewardIds ? unserialize($brandRewardIds) : [];
        if ($brandRewardIds && isset($brandRewardIds[$rewardType])) {
            $values = $brandRewardIds[$rewardType];
            $values[] = $brandId;
            $values = array_unique($values);
            $brandRewardIds[$rewardType] = $values;
        } else {
            $brandRewardIds[$rewardType][] = $brandId;
        }

        $quote->setData('brand_reward_ids', serialize($brandRewardIds));

        $this->importer->import($quote, $quote->getPayment(), $brandId, true);
        $quote->collectTotals();
        $this->quoteRepository->save($quote);

        return true;
    }
}

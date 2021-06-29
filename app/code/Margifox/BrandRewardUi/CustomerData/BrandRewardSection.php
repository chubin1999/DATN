<?php

namespace Margifox\BrandRewardUi\CustomerData;

class BrandRewardSection implements \Magento\Customer\CustomerData\SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface
     */
    private $companyBrandSpendingLevelRepository;

    /**
     * @var \Margifox\BrandReward\Service\GetCurrentCustomerCompany
     */
    private $currentCustomerCompany;

    /**
     * @var \Margifox\BrandRewardUi\Model\BrandLevelRepresentationFactory
     */
    private $brandLevelRepresentationFactory;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository
     * @param \Margifox\BrandReward\Service\GetCurrentCustomerCompany $currentCustomerCompany
     * @param \Margifox\BrandRewardUi\Model\BrandLevelRepresentationFactory $brandLevelRepresentationFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository,
        \Margifox\BrandReward\Service\GetCurrentCustomerCompany $currentCustomerCompany,
        \Margifox\BrandRewardUi\Model\BrandLevelRepresentationFactory $brandLevelRepresentationFactory
    ) {
        $this->customerSession = $customerSession;
        $this->companyBrandSpendingLevelRepository = $companyBrandSpendingLevelRepository;
        $this->currentCustomerCompany = $currentCustomerCompany;
        $this->brandLevelRepresentationFactory = $brandLevelRepresentationFactory;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSectionData()
    {
        $this->customerSession->getCustomer();
        $currentCompany = $this->currentCustomerCompany->execute();
        $levels = $this->companyBrandSpendingLevelRepository->getByCompany($currentCompany);

        $data = [];
        foreach ($levels as $level) {
            /** @var \Margifox\BrandRewardUi\Model\BrandLevelRepresentation $brandLevelRepresentation */
            $brandLevelRepresentation = $this->brandLevelRepresentationFactory->create();
            $brandLevelRepresentation->setBrandSpendingLevel($level);

            $data[] = $brandLevelRepresentation->toArray();
        }

        return [
            'brand_rewards' => $data,
        ];
    }
}

<?php

namespace Margifox\BrandReward\Observer;

use Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface;
use Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterfaceFactory;
use Margifox\BrandReward\Service\GetCurrentCustomerCompany;

class SetCompanyBrands implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var GetCurrentCustomerCompany
     */
    protected $currentCustomerCompany;

    /**
     * @var CompanyBrandSpendingLevelRepositoryInterface
     */
    protected $companyBrandSpendingLevelRepository;

    /**
     * @var CompanyBrandSpendingLevelInterfaceFactory
     */
    protected $companyBrandSpendingLevelInterfaceFactory;

    /**
     * @param GetCurrentCustomerCompany $currentCustomerCompany
     * @param CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository
     * @param CompanyBrandSpendingLevelInterfaceFactory $companyBrandSpendingLevelInterfaceFactory
     */
    public function __construct(
        GetCurrentCustomerCompany $currentCustomerCompany,
        CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository,
        CompanyBrandSpendingLevelInterfaceFactory $companyBrandSpendingLevelInterfaceFactory
    ) {
        $this->currentCustomerCompany = $currentCustomerCompany;
        $this->companyBrandSpendingLevelInterfaceFactory = $companyBrandSpendingLevelInterfaceFactory;
        $this->companyBrandSpendingLevelRepository = $companyBrandSpendingLevelRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Company\Api\Data\CompanyInterface $company */
        $company = $observer->getEvent()->getData('company');

        /** @var array $brandIds */
        $brandIds = $observer->getEvent()->getData('brand_ids') ?: [];

        $companyBrands = $this->companyBrandSpendingLevelRepository->getByCompany($company->getId());
        $companyBrandIds = array_map(function($companyBrand) {
            return $companyBrand->getBrandId();
        }, $companyBrands);

        $companyBrandLevelsToCreate = array_diff($brandIds, $companyBrandIds);
        foreach ($companyBrandLevelsToCreate as $brandId) {
            /** @var \Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface $companyBrandLevel */
            $companyBrandLevel = $this->companyBrandSpendingLevelInterfaceFactory->create();
            $companyBrandLevel->setCompanyId($company->getId())
                ->setBrandId($brandId);
            $this->companyBrandSpendingLevelRepository->save($companyBrandLevel);
        }
    }
}

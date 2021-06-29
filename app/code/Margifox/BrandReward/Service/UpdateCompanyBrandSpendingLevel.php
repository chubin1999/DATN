<?php

namespace Margifox\BrandReward\Service;

class UpdateCompanyBrandSpendingLevel
{
    /**
     * @var \Margifox\BrandReward\Api\CompanyBrandSpendingHistoryRepositoryInterface
     */
    private $companyBrandSpendingHistoryRepository;

    /**
     * @var \Margifox\BrandReward\Provider\SpendingLevelProvider
     */
    private $spendingLevelProvider;

    /**
     * @var \Margifox\BrandReward\Model\Source\SpendingLevel
     */
    private $spendingLevel;

    /**
     * @var \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface
     */
    private $companyBrandSpendingLevelRepository;

    /**
     * @param \Margifox\BrandReward\Api\CompanyBrandSpendingHistoryRepositoryInterface $companyBrandSpendingHistoryRepository
     * @param \Margifox\BrandReward\Provider\SpendingLevelProvider $spendingLevelProvider
     * @param \Margifox\BrandReward\Model\Source\SpendingLevel $spendingLevel
     * @param \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository
     */
    public function __construct(
        \Margifox\BrandReward\Api\CompanyBrandSpendingHistoryRepositoryInterface $companyBrandSpendingHistoryRepository,
        \Margifox\BrandReward\Provider\SpendingLevelProvider $spendingLevelProvider,
        \Margifox\BrandReward\Model\Source\SpendingLevel $spendingLevel,
        \Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository
    ) {
        $this->companyBrandSpendingHistoryRepository = $companyBrandSpendingHistoryRepository;
        $this->spendingLevelProvider = $spendingLevelProvider;
        $this->spendingLevel = $spendingLevel;
        $this->companyBrandSpendingLevelRepository = $companyBrandSpendingLevelRepository;
    }

    /**
     * @param int $companyId
     * @param int $brandId
     */
    public function execute($companyId, $brandId)
    {
        $companyBrandLevel = $this->companyBrandSpendingLevelRepository->getByCompanyAndBrand($companyId, $brandId);

        $salesPerYear = $this->getSalesPerYear($companyId, $brandId);
        $salesPerMonth = $this->getSalesPerMonth($companyId, $brandId);

        $minSalesPerMonthList = $this->spendingLevelProvider->getMinSalesPerMonth($brandId);
        $levelPerMonthAchieved = null;
        foreach ($minSalesPerMonthList as $level => $price) {
            if ($salesPerMonth >= $price) {
                $levelPerMonthAchieved = $level;
            }
        }

        $minSalesPerYearList = $this->spendingLevelProvider->getMinSalesPerYear($brandId);
        $levelPerYearAchieved = null;
        foreach ($minSalesPerYearList as $level => $price) {
            if ($salesPerYear >= $price) {
                $levelPerYearAchieved = $level;
            }
        }

        $newCompanyLevel = $this->getMinLevel($levelPerMonthAchieved, $levelPerYearAchieved);

        if ($companyBrandLevel->getSpendingLevel() != $newCompanyLevel) {
            $this->companyBrandSpendingLevelRepository->save(
                $companyBrandLevel->setSpendingLevel($newCompanyLevel)
            );
        }
    }

    /**
     * @param int $companyId
     * @param int $brandId
     * @return float
     */
    private function getSalesPerYear($companyId, $brandId)
    {
        return $this->companyBrandSpendingHistoryRepository->getCompanyBrandTotalTransactionAmount(
            'P1Y',
            $companyId,
            $brandId
        );
    }

    /**
     * @param int $companyId
     * @param int $brandId
     * @return float
     */
    private function getSalesPerMonth($companyId, $brandId)
    {
        return $this->companyBrandSpendingHistoryRepository->getCompanyBrandTotalTransactionAmount(
            'P1M',
            $companyId,
            $brandId
        );
    }

    /**
     * @param string $levelPerMonthAchieved
     * @param string $levelPerYearAchieved
     * @return string
     */
    private function getMinLevel($levelPerMonthAchieved, $levelPerYearAchieved)
    {
        $spendingLevel = $this->spendingLevel->toOption();
        $spendingLevel = array_values($spendingLevel);

        $monthKey = array_search($levelPerMonthAchieved, $spendingLevel);
        $yearKey = array_search($levelPerYearAchieved, $spendingLevel);
        $minKey = min($monthKey, $yearKey);

        return $spendingLevel[$minKey];
    }
}

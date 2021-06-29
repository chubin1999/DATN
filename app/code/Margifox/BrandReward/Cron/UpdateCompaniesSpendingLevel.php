<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Cron;

use Margifox\BrandReward\Api\CompanyBrandSpendingLevelRepositoryInterface;
use Margifox\BrandReward\Api\Data\CompanyBrandSpendingLevelInterface;
use Margifox\BrandReward\Service\UpdateCompanyBrandSpendingLevel;
use Psr\Log\LoggerInterface;

class UpdateCompaniesSpendingLevel
{
    /**
     * @var CompanyBrandSpendingLevelRepositoryInterface
     */
    private $companyBrandSpendingLevelRepository;

    /**
     * @var UpdateCompanyBrandSpendingLevel
     */
    private $updateCompanyBrandSpendingLevel;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository
     * @param UpdateCompanyBrandSpendingLevel $updateCompanyBrandSpendingLevel
     * @param LoggerInterface $logger
     */
    public function __construct(
        CompanyBrandSpendingLevelRepositoryInterface $companyBrandSpendingLevelRepository,
        UpdateCompanyBrandSpendingLevel $updateCompanyBrandSpendingLevel,
        LoggerInterface $logger
    ) {
        $this->companyBrandSpendingLevelRepository = $companyBrandSpendingLevelRepository;
        $this->updateCompanyBrandSpendingLevel = $updateCompanyBrandSpendingLevel;
        $this->logger = $logger;
    }

    public function execute(): void
    {
        /** @var CompanyBrandSpendingLevelInterface $spendingLevel */
        foreach ($this->companyBrandSpendingLevelRepository->getAll() as $spendingLevel) {
            try {
                $this->updateCompanyBrandSpendingLevel->execute(
                    $spendingLevel->getCompanyId(),
                    $spendingLevel->getBrandId()
                );
            } catch (\Exception $ex) {
                $this->logger->critical($ex->getMessage());
            }
        }
    }
}

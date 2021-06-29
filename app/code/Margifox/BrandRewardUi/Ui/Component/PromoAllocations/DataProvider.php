<?php

namespace Margifox\BrandRewardUi\Ui\Component\PromoAllocations;

use Magento\Framework\App\RequestInterface;
use Margifox\Brand\Api\BrandRepositoryInterface;
use Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingLevel\CollectionFactory as CompanyBrandSpendingLevelFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingLevel\Collection
     */
    private $spendingLevelCollectionFactory;

    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;

    /**
     * @var \Magento\Reward\Model\Reward\Rate
     */
    private $rate;

    /**
     * @param CompanyBrandSpendingLevelFactory $spendingLevelCollectionFactory
     * @param BrandRepositoryInterface $brandRepository
     * @param \Magento\Reward\Model\Reward\Rate $rate
     * @param RequestInterface $request
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CompanyBrandSpendingLevelFactory $spendingLevelCollectionFactory,
        BrandRepositoryInterface $brandRepository,
        \Magento\Reward\Model\Reward\Rate $rate,
        RequestInterface $request,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->spendingLevelCollectionFactory = $spendingLevelCollectionFactory;
        $this->brandRepository = $brandRepository;
        $this->rate = $rate;
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return \Margifox\BrandReward\Model\ResourceModel\CompanyBrandSpendingLevel\Collection
     */
    public function getCollection()
    {
        $collection = parent::getCollection();
        if ($collection === null) {
            $collection = $this->spendingLevelCollectionFactory->create();
        }

        $companyId = $this->request->getParam('company_id') ?? null;
        $collection->addFieldToFilter('company_id', $companyId);

        return $collection;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();

        /** @var array $spendingLevel */
        foreach ($data['items'] as &$spendingLevel) {
            $brand = $this->brandRepository->getById($spendingLevel['brand_id']);
            $spendingLevel['brand'] = $brand->getName();
            $points = $spendingLevel['allocation_points_balance'];

            // TODO: add rate converter per brand
            $currency = $this->rate->calculateToCurrency($points);
            $spendingLevel['current_balance_points'] = $points;
            $spendingLevel['current_balance_dollar'] = $currency;
        }

        return $data;
    }
}

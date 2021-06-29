<?php

namespace Margifox\Brand\Plugin\Company;

class CompanySave
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->serializer = $serializer;
        $this->request = $request;
        $this->eventManager = $eventManager;
    }

    /**
     * @param \Magento\Company\Model\CompanyRepository $companyRepository
     * @param \Magento\Company\Api\Data\CompanyInterface $company
     * @return \Magento\Company\Api\Data\CompanyInterface
     * @throws \Exception
     */
    public function afterSave(
        \Magento\Company\Model\CompanyRepository $companyRepository,
        \Magento\Company\Api\Data\CompanyInterface $company
    ) {
        $generalData = $this->request->getParam('general');

        if (is_array($generalData) && array_key_exists('brand_ids', $generalData)) {
            $company->setBrand($this->serializer->serialize($generalData['brand_ids']));
            $company->save();

            $this->eventManager->dispatch(
                'company_set_brands_after',
                [
                    'company' => $company,
                    'brand_ids' => $generalData['brand_ids']
                ]
            );
        }

        return $company;
    }
}

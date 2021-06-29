<?php

namespace Margifox\BrandReward\Service;

use Magento\Customer\Model\SessionFactory;

class GetCurrentCustomerCompany
{
    /**
     * @var SessionFactory
     */
    private $customerSessionFactory;

    /**
     * @param SessionFactory $customerSessionFactory
     */
    public function __construct(
        SessionFactory $customerSessionFactory
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
    }

    /**
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $customerSession = $this->customerSessionFactory->create();
        if (!$customerSession->isLoggedIn()) {
            return null;
        }

        $extensionAttributes = $customerSession->getCustomerData()->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getCompanyAttributes()) {
            return $extensionAttributes->getCompanyAttributes()->getCompanyId();
        }

        return null;
    }
}

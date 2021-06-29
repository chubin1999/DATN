<?php

namespace Margifox\Brand\Plugin\Company;

class CompanyDataProvider
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param \Magento\Company\Model\Company\DataProvider $subject
     * @param array $result
     * @param \Magento\Company\Api\Data\CompanyInterface $company
     * @return array
     */
    public function afterGetGeneralData(
        \Magento\Company\Model\Company\DataProvider $subject,
        $result,
        \Magento\Company\Api\Data\CompanyInterface $company
    ) {
        if ($company->getId()) {
            $brands = $company->getBrand() ? $this->serializer->unserialize($company->getBrand()) : [];
            $result['brand_ids'] = $brands;
        }

        return $result;
    }
}

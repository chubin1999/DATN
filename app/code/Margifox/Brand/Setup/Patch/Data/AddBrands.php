<?php

namespace Margifox\Brand\Setup\Patch\Data;

use Magento\Eav\Model\Attribute;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddBrands implements DataPatchInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @return $this|DataPatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        /** @var Attribute $attribute */
        $attribute = $this->attributeRepository->get(
            \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID,
            'brand'
            );

        $optionSource = $attribute->getSource();
        $brands = [
            [
                'name' => 'Jane Iredale',
                'points_lifetime' => '60'
            ],
            [
                'name' => 'Environ',
                'points_lifetime' => '60'
            ]
        ];
        foreach ($brands as &$brand) {
            $optionId = $optionSource->getOptionId($brand['name']);
            $brand['attribute_option_link_id'] = $optionId;
        }

        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('brand'),
            ['name', 'points_lifetime', 'attribute_option_link_id'],
            $brands
        );

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }
}

<?php


namespace Margifox\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddProductAtrribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * AddProductAtrribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // add shade attribute
        $eavSetup->addAttribute(Product::ENTITY, 'shade', [
            'type' => 'varchar',
            'label' => 'Shade',
            'input' => 'select',
            'used_in_product_listing' => true,
            'user_defined' => true,
            'required'   => false,
            'visible' => true,
        ]);

        // add product type attribute
        $eavSetup->addAttribute(Product::ENTITY, 'product_type', [
            'type' => 'varchar',
            'label' => 'Product Type',
            'input' => 'select',
            'used_in_product_listing' => true,
            'user_defined' => true,
            'required'   => false,
            'visible' => true,
        ]);
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }
}

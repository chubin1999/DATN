<?php

namespace Margifox\Catalog\Model\Autocomplete\Product;

use Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory as ParentItemFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\AttributeConfig;
use Magento\Framework\View\Asset\Repository;

/**
 * Class ItemFactory
 *
 * @package Bnkr\Search\Model\Autocomplete\Product
 */
class ItemFactory extends ParentItemFactory
{
    /**
     * @var array
     */
    protected $_attributes;

    /**
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * ItemFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ImageHelper $imageHelper
     * @param AttributeConfig $attributeConfig
     * @param Repository $assetRepo
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ImageHelper $imageHelper,
        AttributeConfig $attributeConfig,
        Repository $assetRepo,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($objectManager, $imageHelper, $attributeConfig);
        $this->_attributes = $attributeConfig->getAdditionalSelectedAttributes();
        $this->_storeManager = $storeManager;
        $this->_assetRepo = $assetRepo;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        $product = $data['product'];
        $itemData = parent::create($data);
        $itemData['productsku'] = $product->getSku();
        return $itemData;
    }
}



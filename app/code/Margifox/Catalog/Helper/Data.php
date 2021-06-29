<?php


namespace Margifox\Catalog\Helper;


use Magento\Framework\App\Helper\Context;

use Magento\Store\Model\StoreManagerInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $attribute;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var GetProductSalableQtyInterface
     */
    protected $getProductSalableQty;

    /**
     * @var StockResolverInterface
     */
    protected $stockResolver;

    /**
     * @var \Magento\Swatches\Block\Product\Renderer\Listing\Configurable
     */
    protected $configurable;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @param StoreManagerInterface $storeManager
     * @param StockResolverInterface $stockResolver
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     * @param \Magento\Swatches\Block\Product\Renderer\Listing\Configurable $configurable
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        StockResolverInterface $stockResolver,
        GetProductSalableQtyInterface $getProductSalableQty,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Swatches\Block\Product\Renderer\Listing\Configurable $configurable
    )
    {
        parent::__construct($context);
        $this->attribute = $attribute;
        $this->_storeManager = $storeManager;
        $this->_request = $context->getRequest();
        $this->getProductSalableQty = $getProductSalableQty;
        $this->stockResolver = $stockResolver;
        $this->_productloader = $_productloader;
        $this->configurable = $configurable;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getConfigurableAttributes($product)
    {
        return $product->getTypeInstance()->getConfigurableOptions($product);
    }

    /**
     * @param $attributeId
     * @return string
     */
    public function getAttributeName($attributeId)
    {
        return $this->attribute->load($attributeId)->getName();
    }

    /**
     * @param $productSku
     * @return bool|int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isInstock($productSku)
    {
        return $this->inventoryMessage->isInStock($productSku, $this->getSourceId());
    }

    /**
     * @param $options
     * @return array
     */
    public function getDistinctOptions($options)
    {
        $distinctOptions = [];
        if (!empty($options)) {
            foreach ($options as $option) {
                if (empty($distinctOptions) || array_search($option['value_index'], array_column($distinctOptions, 'value_index')) === false) {
                    $distinctOptions[] = $option;
                }
            }
        }
        return $distinctOptions;
    }

    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }

    public function getQtyProduct($_product)
    {
        $websiteCode = $this->_storeManager->getWebsite()->getCode();
        $stock = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
        $stockId = $stock->getStockId();
        $qty = $this->getProductSalableQty->execute($_product->getSku(), $stockId);
        return $qty;
    }

    /**
     * @return \Magento\Swatches\Block\Product\Renderer\Listing\Configurable
     */
    public function getConfigurableBlock()
    {
        return $this->configurable->setTemplate("Magento_Swatches::product/listing/renderer.phtml");
    }
}

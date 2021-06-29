<?php

namespace Margifox\Email\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 *
 * @package Margifox\Email\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productLoader;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $_imageBuilder;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $_productLoader
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ProductFactory $_productLoader,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->_productLoader = $_productLoader;
        $this->_directoryList = $directoryList;
        $this->_imageBuilder = $imageBuilder;
        $this->_httpContext = $httpContext;
        parent::__construct($context);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getLoadProduct($id)
    {
        return $this->_productLoader->create()->load($id);
    }

    /**
     * @param $id
     * @return mixed|string
     */
    public function getEmailProductImage($id)
    {
        $product = $this->getLoadProduct($id);
        $productImage = $this->getImage($product, 'cart_page_product_thumbnail');
        $imageUrl = $productImage->getImageUrl();
        if ($this->isPubNeeded()) {
            return $imageUrl;
        }
        /* remove pub folder from the url */
        return str_replace('/pub/', '/', $imageUrl);
    }

    /**
     * @param $product
     * @param $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->_imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * @return bool
     */
    public function isPubNeeded()
    {
        $pub = $this->_directoryList->getUrlPath('pub');
        if ($pub == 'pub') {
            return true;
        } else {
            return false;
        }
    }
}

<?php
namespace Margifox\Search\Plugin\Autocomplete\Product;

use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Pricing\Render;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use \Magento\Framework\Data\Form\FormKey;

class afterItemFactory extends \Magento\Search\Model\Autocomplete\ItemFactory
{
    /**
     * Autocomplete image id (used for resize)
     */
    const AUTOCOMPLETE_IMAGE_ID = 'smile_elasticsuite_autocomplete_product_image';

    /**
     * @var ImageHelper
     */
    private $imageHelper;


    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Framework\Pricing\Render
     */
    private $priceRenderer = null;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Constructor.
     *
     * @param ObjectManagerInterface $objectManager   Object manager used to instantiate new item.
     * @param ImageHelper            $imageHelper     Catalog product image helper.
     * @param AttributeConfig        $attributeConfig Autocomplete attribute config.
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ImageHelper $imageHelper,
        UrlHelper $urlHelper,
        \Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\AttributeConfig $attributeConfig
    ) {
        $this->urlHelper     = $urlHelper;
        parent::__construct($objectManager);
        $this->attributes    = $attributeConfig->getAdditionalSelectedAttributes();
        $this->imageHelper   = $imageHelper;
        $this->objectManager = $objectManager;
    }
    /**
     * @param Onepage $subject
     * @param $result
     * @return mixed
     */

    public function aroundCreate($subjet, callable $proceed, $data) {
        $data = $this->addProductData($data);
        unset($data['product']);

        return parent::create($data);
    }
    private function addProductData($data)
    {
        $product = $data['product'];

        $product_id = $product->getId();
        $_product = $this->objectManager->create('Magento\Catalog\Model\Product')->load($product_id);

        $productData = [
            'title' => $product->getName(),
            'image' => $this->getImageUrl($product),
            'url'   => $product->getProductUrl(),
            'price' => $this->renderProductPrice($product, \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE),
            'sku'   => $product->getSku(),
            'brand' => $_product->getResource()->getAttribute('brand')->getFrontend()->getValue($_product)
            // 'brand' => $product->getAttributeText('brand')
        ];

        // foreach ($this->attributes as $attributeCode) {
        //     if ($product->hasData($attributeCode)) {
        //         $productData[$attributeCode] = $product->getData($attributeCode);
        //         if ($product->getResource()->getAttribute($attributeCode)->usesSource()) {
        //             $productData[$attributeCode] = $product->getAttributeText($attributeCode);
        //         }
        //     }
        // }

        $data = array_merge($data, $productData);

        return $data;
    }
    private function getImageUrl($product)
    {
        $this->imageHelper->init($product, self::AUTOCOMPLETE_IMAGE_ID);

        return $this->imageHelper->getUrl();
    }


    /**
     * Renders product price.
     *
     * @param \Magento\Catalog\Model\Product $product   The product
     * @param string                         $priceCode The Price Code to render
     *
     * @return string
     */
    private function renderProductPrice(\Magento\Catalog\Model\Product $product, $priceCode)
    {
        $priceRender = $this->getPriceRenderer();

        $price = $product->getData($priceCode);

        if ($priceRender) {
            $price = $priceRender->render(
                $priceCode,
                $product,
                [
                    'include_container' => false,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true,
                ]
            );
        }

        return $price;
    }

    /**
     * Retrieve Price Renderer Block
     *
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     */
    private function getPriceRenderer()
    {
        if (null === $this->priceRenderer) {
            /** @var \Magento\Framework\View\LayoutInterface $layout */
            $layout = $this->objectManager->get('\Magento\Framework\View\LayoutInterface');
            $layout->getUpdate()->addHandle('default');
            $priceRenderer = $layout->getBlock('product.price.render.default');

            if (!$priceRenderer) {
                $priceRenderer = $layout->createBlock(
                    'Magento\Framework\Pricing\Render',
                    'product.price.render.default',
                    ['data' => ['price_render_handle' => 'catalog_product_prices']]
                );
            }

            $this->priceRenderer = $priceRenderer;
        }

        return $this->priceRenderer;
    }
}
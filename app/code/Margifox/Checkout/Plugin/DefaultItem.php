<?php

namespace Margifox\Checkout\Plugin;

use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Api\ProductRepositoryInterface;

class DefaultItem
{
	/**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function aroundGetItemData($subject, \Closure $proceed, Item $item)
    {
        $data = $proceed($item);
        $product = $item->getProduct();

        $atts = [
            "product_brand" => $product->getAttributeText('brand')
        ];

        return array_merge($data, $atts);
    }

    public function afterGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject,$result,\Magento\Quote\Model\Quote\Item $item)
    {

        $data['options'] = $result['options'];

        $product_sku = $result['product_sku'];

        $child_product = $this->loadMyProduct($product_sku);

        if($result['product_type'] == 'configurable') {

        	$value_shade = '';

        	$value_product_type_swatch = '';

        	if($child_product->getShade()) {

        		$value_shade = $child_product->getResource()->getAttribute('shade')->getFrontend()->getValue($child_product);

        	}

        	if($child_product->getProductTypeSwatch()) {

        		$value_product_type_swatch = $child_product->getResource()->getAttribute('product_type_swatch')->getFrontend()->getValue($child_product);

        	}

	        foreach ($data['options'] as $key => $option) {

	        	if($option['label'] == 'Shade') {

	        		$data['options'][$key]['value'] = $value_shade;

	        	}

	        	if($option['label'] == 'Product type swatch' || $option['label'] == 'Product type') {

	        		$data['options'][$key]['value'] = $value_product_type_swatch;

	        	}

	        }

        }

        return array_merge($result,$data);

    }


    public function loadMyProduct($sku)
    {
        return $this->productRepository->get($sku);
    }
    
}
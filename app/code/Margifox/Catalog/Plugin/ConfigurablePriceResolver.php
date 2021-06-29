<?php

namespace Margifox\Catalog\Plugin;
use Magento\Checkout\Model\Session;

class ConfigurablePriceResolver
{
    protected $_session;
    public function __construct(Session $session)
    {
        $this->_session = $session;
    }

    public function afterResolvePrice(\Magento\ConfigurableProduct\Pricing\Price\ConfigurablePriceResolver $subject, $priceOld, \Magento\Framework\Pricing\SaleableInterface $product)
    {
        // if(empty($priceOld)) {
            if($product->getTypeId() == 'configurable' && !$product->isSaleable()) {
                $price = null; 
                $_children = $product->getTypeInstance()->getUsedProducts($product);
                foreach ($_children as $subProduct) {
                    $productPrice = $subProduct->getPrice();
                    $price = isset($price) ? min($price, $productPrice) : $productPrice;
                }
                return $price;
            }
        // }
        return $priceOld;
    }

}


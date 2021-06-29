<?php

namespace Margifox\Checkout\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class Data
 *
 * @package Margifox\Checkout\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Configurable
     */
    protected $configurable;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        Configurable $configurable
    )
    {
        $this->productRepository = $productRepository;
        $this->configurable = $configurable;
        parent::__construct($context);
    }

    public function loadMyProduct($sku)
    {
        return $this->productRepository->get($sku);
    }

    public function loadMyProductById($id)
    {
        return $this->productRepository->getById($id);
    }

}

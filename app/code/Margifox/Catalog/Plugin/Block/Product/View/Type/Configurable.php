<?php

namespace Margifox\Catalog\Plugin\Block\Product\View\Type;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as Subject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Configurable
 *
 * @package Margifox\Catalog\Plugin\Block\Product\View\Type
 */
class Configurable
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var GetProductSalableQtyInterface
     */
    private $getProductSalableQty;

    /**
     * @var EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var DecoderInterface
     */
    private $jsonDecoder;

    /**
     * Configurable constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StockResolverInterface $stockResolver
     * @param StoreManagerInterface $storeManager
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param EncoderInterface $jsonEncoder
     * @param DecoderInterface $jsonDecoder
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StockResolverInterface $stockResolver,
        StoreManagerInterface $storeManager,
        GetProductSalableQtyInterface $getProductSalableQty,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->stockResolver = $stockResolver;
        $this->storeManager = $storeManager;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetJsonConfig(Subject $subject, $result)
    {
        $config = $this->jsonDecoder->decode($result);
        $childProducts = $subject->getAllowProducts();
        $quantities = [];
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        $stock = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
        $stockId = $stock->getStockId();
        foreach ($childProducts as $_product) {
            $qty = $this->getProductSalableQty->execute($_product->getSku(), $stockId);
            $quantities[$_product->getId()] = $qty;
        }
        $config['qtys'] = $quantities;
        return $this->jsonEncoder->encode($config);
    }
}

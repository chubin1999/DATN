<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Margifox\Checkout\Plugin\Model;

use Magento\Checkout\Model\Session as CheckoutSession;


/**
 * Default item
 */
class DefaultConfigProvider
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\ProductFactory
    */
    protected $productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;
    
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Margifox\Checkout\Model\CompositeConfigProvider
     */
    protected $configProvider;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\ProductFactory $productFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Session $checkoutSession
     * @param \Margifox\Checkout\Model\CompositeConfigProvider $configProvider
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CheckoutSession $checkoutSession,
        \Margifox\Checkout\Model\CompositeConfigProvider $configProvider
    ) {
        $this->productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->configProvider = $configProvider;
    }
    
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result) {
        $items = $result['totalsData']['items'];
        $items = $this->getTotalsData($items);
        $result['totalsData']['items'] =  $items;
        return  $result;
    }
    
    /**
     * Return quote totals data
     *
     * @return array
     */
    private function getTotalsData($items)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $poductAttribute = $this->productFactory->create();
        $data = [];
        foreach(array_reverse($this->checkoutSession->getQuote()->getAllVisibleItems()) as $item){
            $product = $item->getProduct();
            $data[$item->getId()] = $this->configProvider->getAttributeData($poductAttribute, $product, $storeId);
            
            /*foreach($this->map as $code => $vals){
                $data[$item->getId()][$code]['label'] = $vals['title'];
                $instance = $this->objectManager->create($vals['object'], []);
                $data[$item->getId()][$code]['value'] = $instance->getValue($poductAttribute, $product, $storeId, $code);
            }*/
        }
        foreach($items as $k => $_item){
            if(isset($data[$_item['item_id']])){
                $items[$k]['attributes'] = $data[$_item['item_id']];
            }
        }
        return $items;
    }

}
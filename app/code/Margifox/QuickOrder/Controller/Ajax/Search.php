<?php

namespace Margifox\QuickOrder\Controller\Ajax;

use Magento\AdvancedCheckout\Model\Cart;

class Search extends \Magento\QuickOrder\Controller\Ajax\Search
{
    /**
     * Get info about products, which SKU specified in request
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $requestData = $this->getRequest()->getPostValue();
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $generalErrorMessage = '';
        $items = json_decode($requestData['items'], true);
        $items = $this->removeEmptySkuItems($items);
        if (empty($items)) {
            $generalErrorMessage = __('Data does not contain valid product SKUs.'); // updated
        } else {
            $this->cart->setContext(Cart::CONTEXT_FRONTEND);
            $this->cart->prepareAddProductsBySku($items);
            $items = $this->cart->getAffectedItems();
        }

        $data = [
            'generalErrorMessage' => (string) $generalErrorMessage,
            'items' => $items,
        ];

        return $resultJson->setData($data);
    }
}

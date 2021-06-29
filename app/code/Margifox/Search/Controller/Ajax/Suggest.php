<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Margifox\Search\Controller\Ajax;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Framework\Controller\ResultFactory;
use Smile\ElasticsuiteCatalog\Helper\Autocomplete as ConfigurationHelper;

class Suggest extends Action implements HttpGetActionInterface
{
    /**
     * @var  \Magento\Search\Model\AutocompleteInterface
     */
    private $autocomplete;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Search\Model\AutocompleteInterface $autocomplete
     */
    public function __construct(
        ConfigurationHelper $configurationHelper,
        Context $context,
        AutocompleteInterface $autocomplete
    ) {
        $this->autocomplete = $autocomplete;
        $this->configurationHelper = $configurationHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }

        $autocompleteData = $this->autocomplete->getItems();
        $responseData = [];
        $checkHasTerm = false;
        $count = 0;
        $limit = false;
        $limitShowProduct = $this->getResultsPageSize();
        foreach ($autocompleteData as $resultItem) {
            $termOg = $this->getRequest()->getParam('q', false);
            $dataItem = $resultItem->toArray();
            if(array_key_exists('type',$dataItem)){
                if($dataItem['type'] == 'term'){
                    $checkHasTerm = true;
                    $strlenOG = strlen($termOg);
                    $dataItem['keyOg'] =  $termOg;
                    $dataItem['keyTerm'] = substr($dataItem['title'],$strlenOG);
                }
            }
            // if(array_key_exists('type',$dataItem)){
            //     if($dataItem['type'] == 'product'){
            //        $count++;
            //        if($count > $limitShowProduct){
            //             $limit = true;
            //             continue;
            //        }
            //     }
            // }
            $responseData[] = $dataItem;
        }
        // if($limit == true){
        //     $responseData[] = array('type' => 'product', 'see_all' => $count);
        // }
        if($checkHasTerm == false){
            $responseData[] = array('type' => 'term', 'data' => '0');
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }
    private function getResultsPageSize()
    {
        return $this->configurationHelper->getMaxSize('product');
    }
}

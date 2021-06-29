<?php
namespace Margifox\Customer\Plugin\Result;
use Magento\Framework\App\ResponseInterface;
class Page
{
    /**
    * @var \Margifox\Customer\Helper\Data
    */
    protected $data;


    public function __construct(
        \Margifox\Customer\Helper\Data $data
    ) {
        $this->data = $data;
    }

    public function beforeRenderResult(\Magento\Framework\View\Result\Page $subject,ResponseInterface $response)
    {
    // "cms_index_index"
    // "catalog_product_view"
    // "catalog_category_view"
    // "catalogsearch_result_index"
    if($this->data->getFullActionName() == "cms_index_index" || $this->data->getFullActionName() == "catalog_product_view" || $this->data->getFullActionName() == "catalog_category_view" || $this->data->getFullActionName() == "catalogsearch_result_index") {
        $subject->getConfig()->addBodyClass('style-message');
    }

    if($this->data->getLogin()){
       $subject->getConfig()->addBodyClass('logged');
    } else{
       $subject->getConfig()->addBodyClass('logout');
    }

    return [$response];
    }
} 
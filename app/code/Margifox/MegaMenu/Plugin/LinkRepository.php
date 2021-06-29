<?php
namespace Margifox\MegaMenu\Plugin;

class LinkRepository
{
    protected $request;

    public function __construct(\Magento\Framework\App\Request\Http $request)
    {
        $this->request = $request;
    }


    public function afterSave(\Amasty\MegaMenu\Api\LinkRepositoryInterface $subject, $model)
    {
        $data = $this->request->getParams();
        echo '<pre>';
        print_r($data);
        echo '<pre>';

        echo '<pre>';
        print_r($model->getData());
        echo '<pre>';
        die("12");
        
        return $model;

    }



}

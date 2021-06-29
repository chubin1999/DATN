<?php
namespace Margifox\OrderHistory\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_request;

    private $_objectManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    )
    {
        parent::__construct($context);
        $this->_request = $context->getRequest();
        $this->_objectManager = $objectmanager;
    }
    
    public function getOrder()
    {
        $orderId = $this->_request->getParams();
        if ($orderId) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            return $order;
        }
        return false;
    }



}






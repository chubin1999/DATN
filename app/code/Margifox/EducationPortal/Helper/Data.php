<?php
namespace Margifox\EducationPortal\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Data constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
   {
       parent::__construct($context);
   }

    /**
     * @return mixed
     */
    public function getPageTitle()
   {
       return $this->scopeConfig->getValue('education/general/title', ScopeInterface::SCOPE_STORE);
   }

    /**
     * @return mixed
     */
    public function getLimitItems()
   {
       return $this->scopeConfig->getValue('education/general/limit', ScopeInterface::SCOPE_STORE);
   }
}






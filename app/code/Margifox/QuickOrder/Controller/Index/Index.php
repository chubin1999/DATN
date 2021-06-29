<?php


namespace Margifox\QuickOrder\Controller\Index;


use Magento\Framework\View\Result\PageFactory;
use Magento\QuickOrder\Model\Config as ModuleConfig;

class Index extends \Magento\QuickOrder\Controller\Index\Index
{
    /**
     * @var \Margifox\Customer\Helper\Data
     */
    protected $customerHelper;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param ModuleConfig $moduleConfig
     * @param PageFactory $resultPageFactory
     * @param \Margifox\Customer\Helper\Data $customerHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ModuleConfig $moduleConfig,
        PageFactory $resultPageFactory,
        \Margifox\Customer\Helper\Data $customerHelper
    )
    {
        parent::__construct($context, $moduleConfig, $resultPageFactory);
        $this->customerHelper = $customerHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // redirect to login page
        if (!$this->customerHelper->getLogin()) {
            return $this->_redirect('customer/account');
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Quick Order'));

        return $resultPage;
    }
}

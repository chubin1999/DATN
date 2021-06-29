<?php


namespace Margifox\EducationPortal\Controller\Portal;


class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Margifox\EducationPortal\Helper\Data
     */
    protected $educationHelper;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Margifox\EducationPortal\Helper\Data $educationHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Margifox\EducationPortal\Helper\Data $educationHelper
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->educationHelper = $educationHelper;
        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $pageTitle = $this->educationHelper->getPageTitle() ? $this->educationHelper->getPageTitle() : 'Education Portal';
        $pageResult = $this->_pageFactory->create();
        $pageResult->getConfig()->getTitle()->set($pageTitle);
        return $pageResult;
    }
}

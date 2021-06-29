<?php

namespace Margifox\EducationPortal\Controller\Adminhtml\Portal;

use Margifox\EducationPortal\Model\Portal as PortalModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Margifox\EducationPortal\Model\PortalFactory;
use Magento\Backend\Model\Session;

class Edit extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PortalFactory
     */
    private $portalFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PortalFactory $portalFactory
     * @param Registry $registry
     * @param Session $session
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PortalFactory $portalFactory,
        Registry $registry,
        Session $session
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->session = $session;
        $this->portalFactory = $portalFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Margifox_EducationPortal::grid')
            ->addBreadcrumb(__('Education Portal'), __('Education Portal'))
            ->addBreadcrumb(__('Education Portal Information'), __('Education Portal Information'));
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var PortalModel $portal */
        $portal = $this->portalFactory->create();
        if ($id) {
            $portal->load($id);
            if (!$portal->getId()) {
                $message = __('Oops something went wrong. we cannot find the portal, please try again.') ;
                $this->messageManager->addErrorMessage($message);
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $portal->setData($data);
        }

        $this->_coreRegistry->register('portal_grid', $portal);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Education Portal') : __('New Education Portal'),
            $id ? __('Edit Education Portal') : __('New Education Portal')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Education Portal'));
        $resultPage->getConfig()->getTitle()
            ->prepend($portal->getId() ? $portal->getName() : __('New Education Portal'));

        return $resultPage;
    }
}

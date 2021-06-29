<?php

namespace Margifox\EducationPortal\Controller\Adminhtml\Portal;

use Margifox\EducationPortal\Model\Portal as PortalModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Margifox\EducationPortal\Model\PortalFactory;

class Delete extends Action
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
     * Delete constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PortalFactory $portalFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PortalFactory $portalFactory,
        Registry $registry
    )
    {

        $this->portalFactory = $portalFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $originalRequestData = $this->getRequest()->getParams();
        if (!empty($originalRequestData['key'])) {
            if ($originalRequestData) {
                try {
                    /** @var PortalModel $portal */
                    $portal = $this->portalFactory->create();
                    if (!empty($originalRequestData['id'])) {
                        $portal = $portal->load($originalRequestData['id']);
                        $portal->delete();
                    }
                    $this->messageManager->addSuccessMessage(__('You successfully deleted the portal.'));
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
                } catch (\Exception $e) {
                    $message = __('Oops we ran into an error deleting the portal, %1', $e->getMessage()) ;
                    $this->messageManager->addErrorMessage($message);
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
                }
            }
        }
        $message = __('Oops something went wrong. we cannot find the portal, please try again.') ;
        $this->messageManager->addErrorMessage($message);
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}

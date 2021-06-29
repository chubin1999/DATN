<?php


namespace Margifox\EducationPortal\Controller\Adminhtml\Portal;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Margifox\EducationPortal\Model\PortalFactory;
use Margifox\EducationPortal\Model\Portal as PortalModel;

class MassDelete extends Action
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
     * MassDelete constructor.
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $isSelected = $this->getRequest()->getParam('selected');
        $processedNumber = 0;
        $errorNumber = 0;
        try {
            if (!empty($isSelected)) {
                foreach ($isSelected as $items) {
                    /** @var PortalModel $portal */
                    $portal = $this->portalFactory->create();
                    if ($items) {
                        $portal->load($items);
                        if (!$portal->getId()) {
                            $message = __('Oops something went wrong. we cannot find the portal, please try again.');
                            $this->messageManager->addErrorMessage($message);
                            $errorNumber++;
                            continue;
                        }
                        try {
                            $portal->delete();
                            $processedNumber++;
                        } catch (\Exception $e) {
                            $message = __('Oops we ran into an error deleting the portal, %1', $e->getMessage());
                            $this->messageManager->addErrorMessage($message);
                            $errorNumber++;
                            continue;
                        }
                    }
                }
            } else {
                /** @var PortalModel $portal */
                $portal = $this->portalFactory->create();
                foreach ($portal->getCollection() as $item) {
                    try {
                        $item->delete();
                        $processedNumber++;
                    } catch (\Exception $e) {
                        $message = __('Oops we ran into an error deleting the portal, %1', $e->getMessage());
                        $this->messageManager->addErrorMessage($message);
                        $errorNumber++;
                        continue;
                    }
                }
            }
            if ($processedNumber > 0) {
                $this->messageManager->addSuccessMessage(__('A total of %1 portal(s) have been deleted.', $processedNumber));
            }
            if ($errorNumber > 0) {
                $this->messageManager->addErrorMessage(__('A total of %1 portal(s) was not deleted.', $errorNumber));
            }
        } catch (\Exception $e) {
            $message = __('Oops something went wrong, please try again. Error %1', $e->getMessage());
            $this->messageManager->addErrorMessage($message);
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}

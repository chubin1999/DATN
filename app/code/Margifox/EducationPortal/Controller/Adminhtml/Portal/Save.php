<?php

namespace Margifox\EducationPortal\Controller\Adminhtml\Portal;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Margifox\EducationPortal\Model\PortalFactory;
use Margifox\EducationPortal\Model\Portal as PortalModel;
use Magento\MediaStorage\Model\File\UploaderFactory as Uploader;
use Magento\Framework\Exception\LocalizedException;


class Save extends Action
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
     * @var uploadPageFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var PortalFactory
     */
    private $portalFactory;

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnect;
    /**
     * Save constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $_storeManager
     * @param PortalFactory $portalFactory
     * @param Filesystem $fileSystem
     * @param Uploader $uploader
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $_storeManager,
        PortalFactory $portalFactory,
        Filesystem $fileSystem,
        Uploader $uploader,
        \Magento\Framework\App\ResourceConnection $resourceConnect
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->uploaderFactory = $uploaderFactory;
        $this->_storeManager = $_storeManager;
        $this->fileSystem = $fileSystem;
        $this->portalFactory = $portalFactory;
        $this->uploader = $uploader;
        $this->_resourceConnect = $resourceConnect;
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
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getParams();

        if (!empty($originalRequestData['back']) && $originalRequestData['back'] == 'edit') {
            $returnToEdit = true;
        }
        if (!empty($originalRequestData['form_key'])) {
            if ($originalRequestData) {
                try {
                    $data = array(
                        'name' => $originalRequestData['name'],
                        'status' => isset($originalRequestData['status']) ? 1 : 0,
                        'description' => $originalRequestData['description'],
                        'video' => $this->generateVideoEmbedUrl($originalRequestData['video']),
                        'brand' => !empty($originalRequestData['brand']) ? implode(',', $originalRequestData['brand']) : null,
                    );
                    $file = $this->uploadFile();

                    if (!empty($file)) {
                        $data['pdf'] = $file;
                    }
                    /** @var PortalModel $portal */
                    $portal = $this->portalFactory->create();
                    if (!empty($originalRequestData['id'])) {
                        $id = $originalRequestData['id'];
                        $portal->load($id);
                        if (!$portal->getId()) {
                            $message = __('Oops something went wrong. we cannot find the education portal, please try again.') ;
                            $this->messageManager->addErrorMessage($message);
                            /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                            $resultRedirect = $this->resultRedirectFactory->create();
                            return $resultRedirect->setPath('*/*/');
                        }
                    }
                    $portal->addData($data);
                    $portal->save();
                    $this->messageManager->addSuccessMessage(__('You successfully saved the portal.'));
                    if ($returnToEdit) {
                        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/edit', ['id' => $portal->getId()]);
                    }
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');

                } catch (\Exception $e) {
                    $message = __('Oops we ran into an error saving the portal, %1', $e->getMessage()) ;
                    $this->messageManager->addErrorMessage($message);
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/edit', ['id' => $portal->getId()]);
                }
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function uploadFile()
    {
        $destinationPath = $this->getDestinationPath();
        $pdf = $this->getRequest()->getFiles('pdf');
        $fileName = ($pdf && array_key_exists('name', $pdf)) ? $pdf['name'] : null;
        if ($pdf && $fileName) {
            try {
                /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
                $uploader = $this->uploader->create(['fileId' => 'pdf']);
                $uploader->setAllowedExtensions(['pdf', 'csv', 'docx']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                if (!$result = $uploader->save($destinationPath)) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                return $result['file'];
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return '';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getDestinationPath()
    {
        return $this->fileSystem
            ->getDirectoryWrite(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            ->getAbsolutePath('/portal/pdf');
    }

    /**
     * @param $url
     * @return string
     */
    public function generateVideoEmbedUrl($url)
    {
        //This is a general function for generating an embed link of an FB/Vimeo/Youtube Video.
        $finalUrl = '';
        if (strpos($url, 'facebook.com/') !== false) {
            //it is FB video
            $finalUrl .= 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode($url) . '&show_text=1&width=200';
        } else if (strpos($url, 'vimeo.com/') !== false) {
            //it is Vimeo video
            $videoId = explode("vimeo.com/", $url)[1];
            if (strpos($videoId, '&') !== false) {
                $videoId = explode("&", $videoId)[0];
            }
            $finalUrl .= 'https://player.vimeo.com/video/' . $videoId;
        } else if (strpos($url, 'youtube.com/') !== false) {
            //it is Youtube video
            $videoId = explode("v=", $url)[1];
            if (strpos($videoId, '&') !== false) {
                $videoId = explode("&", $videoId)[0];
            }
            $finalUrl .= 'https://www.youtube.com/embed/' . $videoId;
        } else if (strpos($url, 'youtu.be/') !== false) {
            //it is Youtube video
            $videoId = explode("youtu.be/", $url)[1];
            if (strpos($videoId, '&') !== false) {
                $videoId = explode("&", $videoId)[0];
            }
            $finalUrl .= 'https://www.youtube.com/embed/' . $videoId;
        } else {
            $finalUrl .= $url;
        }
        return $finalUrl;
    }
}

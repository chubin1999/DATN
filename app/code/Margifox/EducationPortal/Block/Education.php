<?php


namespace Margifox\EducationPortal\Block;


use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;

class Education extends Template
{
    const EDUCATION_PORTAL_PATH = 'portal/pdf';
    const LIMIT_DEFAULT = 15;
    /**
     * @var \Margifox\EducationPortal\Model\ResourceModel\Portal\Collection
     */
    protected $portalCollection;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Margifox\EducationPortal\Helper\Data
     */
    protected $educationHelper;

    /**
     * Education constructor.
     * @param Template\Context $context
     * @param \Margifox\EducationPortal\Model\ResourceModel\Portal\Collection $portalCollection
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Margifox\EducationPortal\Helper\Data $educationHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Margifox\EducationPortal\Model\ResourceModel\Portal\Collection $portalCollection,
        \Magento\Eav\Model\Config $eavConfig,
        \Margifox\EducationPortal\Helper\Data $educationHelper,
        array $data = []
    )
    {
        $this->portalCollection = $portalCollection;
        $this->eavConfig = $eavConfig;
        $this->educationHelper = $educationHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Margifox\EducationPortal\Model\ResourceModel\Portal\Collection
     */
    public function getCollection()
    {
        $brand = $this->getRequest()->getParam('brand');
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = $this->educationHelper->getLimitItems() ? $this->educationHelper->getLimitItems() : self::LIMIT_DEFAULT;
        $collection = $this->portalCollection->addFieldToSelect("*")
            ->addFieldToFilter('status', 1);
        if ($brand) {
            $collection->addFieldToFilter('brand',
                [
                    ['finset' => $brand]
                ]);
        }
        $collection->setOrder('id', 'desc')
            ->setPageSize($pageSize)
            ->setCurPage($page);
        return $collection;
    }

    /**
     * @param $document
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDocumentUrl($document)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::EDUCATION_PORTAL_PATH . $document;
    }

    /**
     * @return $this|Education
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'education.portal.pager'
            )
                ->setCollection($this->getCollection());
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBrands()
    {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, 'brand');
        $options = $attribute->getSource()->getAllOptions();
        if (count($options)) {
            foreach ($options as $key => $option) {
                if (!$option['value']) {
                    unset($options[$key]);
                }
            }
        }
        return $options;
    }
}

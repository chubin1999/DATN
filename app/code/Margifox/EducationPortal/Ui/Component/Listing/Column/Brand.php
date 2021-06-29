<?php


namespace Margifox\EducationPortal\Ui\Component\Listing\Column;


use Magento\Catalog\Model\Product;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Brand extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * Brand constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Eav\Model\Config $eavConfig,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item) {
                    $brands = $item['brand'] ? explode(',', $item['brand']) : [];
                    $attribute = $this->eavConfig->getAttribute(Product::ENTITY, 'brand');
                    $brandLabels = [];
                    if (!empty($brands)) {
                        foreach ($brands as $brand) {
                            $brandLabels[] = $attribute->getSource()->getOptionText($brand);
                        }
                    }
                    $item['brand'] = implode(', ', $brandLabels);
                }
            }
        }

        return $dataSource;
    }

}

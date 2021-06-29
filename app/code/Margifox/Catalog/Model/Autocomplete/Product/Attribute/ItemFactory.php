<?php

namespace Margifox\Catalog\Model\Autocomplete\Product\Attribute;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;

class ItemFactory extends \Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\Attribute\ItemFactory
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * ItemFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param UrlInterface $urlBuilder
     * @param ProductAttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        UrlInterface $urlBuilder,
        ProductAttributeRepositoryInterface $attributeRepository
    )
    {
        parent::__construct($objectManager, $urlBuilder);
        $this->urlBuilder = $urlBuilder;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        $data['title'] = $data['value'];
        $data['url']   = $this->getUrl($data);
        unset($data['value']);
        return \Magento\Search\Model\Autocomplete\ItemFactory::create($data);
    }

    /**
     * Returns autocompelete result URL.
     *
     * @param array $data Autocomplete data.
     *
     * @return string
     */
    private function getUrl(array $data)
    {
        $urlParams = ['q' => $data['value'], $data['attribute_code'] => $data['value']];

        return $this->urlBuilder->getUrl('catalogsearch/result', ['_query' => $urlParams]);
    }
}

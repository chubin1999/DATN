<?php

namespace Margifox\Brand\Model\Brand;

class BrandOptionsProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Margifox\Brand\Model\ResourceModel\Brand\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \Margifox\Brand\Model\ResourceModel\Brand\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Margifox\Brand\Model\ResourceModel\Brand\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var \Margifox\Brand\Model\ResourceModel\Brand\Collection $collection */
        $collection = $this->collectionFactory->create();
        foreach ($collection as $item) {
            $options[] = [
                'value' => $item->getId(),
                'label' => $item->getName()
            ];
        }

        return $options ?? [];
    }
}

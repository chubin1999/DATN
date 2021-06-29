<?php
declare(strict_types=1);

namespace Margifox\BrandReward\Block\Adminhtml\Form\Field;

class BrandColumn extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var \Margifox\Brand\Model\ResourceModel\Brand\Collection
     */
    private $brandCollection;

    /**
     * @param \Margifox\Brand\Model\ResourceModel\Brand\Collection $brandCollection
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $data
     */
    public function __construct(
        \Margifox\Brand\Model\ResourceModel\Brand\Collection $brandCollection,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        $this->brandCollection = $brandCollection;
        parent::__construct($context, $data);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    /**
     * @return array
     */
    private function getSourceOptions(): array
    {
        $options = [];
        foreach ($this->brandCollection->getItems() as $item) {
            $options[] = [
                'label' => $item['name'],
                'value' => mb_strtolower($item['entity_id'])
            ];
        }

        return $options;
    }
}

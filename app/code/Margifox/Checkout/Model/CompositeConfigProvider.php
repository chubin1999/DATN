<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Margifox\Checkout\Model;

use Magento\Framework\ObjectManagerInterface;

/**
 * Composite checkout configuration provider.
 *
 * @api
 * @since 100.0.2
 */
class CompositeConfigProvider
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var map[]
     */
    protected $map;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param [] $map
     * @codeCoverageIgnore
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $map
    ) {
        $this->objectManager = $objectManager;
        $this->map = $map;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeData($poductAttribute, $product, $storeId)
    {
        $data = [];
        foreach($this->map as $code => $vals){
            $data[$code]['label'] = $vals['title'];
            $instance = $this->objectManager->create($vals['object'], []);
            $data[$code]['value'] = $instance->getValue($poductAttribute, $product, $storeId, $code);
        }
        return $data;
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Margifox\Checkout\Model\Attribute;

/**
 * Default item
 */
class Dropdown
{
    /**
     * Return quote totals data
     *
     * @return array
     */
    public function getValue($poductAttribute, $product, $storeId, $attrCode)
    {
        $optionId = $product->getResource()->getAttributeRawValue(
            $product->getId(),
            $attrCode,
            $storeId
        );
        $optionText = '';
        if($optionId){
            $attribute = $poductAttribute->getAttribute($attrCode);
            if ($attribute->usesSource()) {
                $optionText = $attribute->getSource()->getOptionText($optionId);
            }
        }
        return $optionText;
    }
}
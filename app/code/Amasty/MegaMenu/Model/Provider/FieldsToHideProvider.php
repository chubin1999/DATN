<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Model\Provider;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;

class FieldsToHideProvider
{
    const CATEGORY_LEVEL_ERROR = 'category_level_error';

    public function getRootCategoryFields(): array
    {
        return [
            ItemInterface::WIDTH,
            ItemInterface::WIDTH_VALUE,
            ItemInterface::COLUMN_COUNT,
            ItemInterface::LABEL,
            ItemInterface::LABEL_GROUP,
            ItemInterface::CONTENT,
            ItemInterface::SUBMENU_TYPE,
            ItemInterface::SUBCATEGORIES_POSITION,
            ItemInterface::ICON
        ];
    }

    public function getMainCategoryFields(): array
    {
        return [self::CATEGORY_LEVEL_ERROR];
    }

    public function getSubCategoryFields(bool $isShowSubcategories = false): array
    {
        if ($isShowSubcategories) {
            $itemsToHide = [
                ItemInterface::WIDTH,
                ItemInterface::WIDTH_VALUE,
                ItemInterface::COLUMN_COUNT,
                ItemInterface::SUBMENU_TYPE,
                self::CATEGORY_LEVEL_ERROR
            ];
        } else {
            $itemsToHide = [
                ItemInterface::WIDTH,
                ItemInterface::WIDTH_VALUE,
                ItemInterface::COLUMN_COUNT,
                ItemInterface::LABEL,
                ItemInterface::LABEL_GROUP,
                ItemInterface::CONTENT,
                ItemInterface::SUBMENU_TYPE,
                ItemInterface::SUBCATEGORIES_POSITION
            ];
        }

        return $itemsToHide;
    }
}

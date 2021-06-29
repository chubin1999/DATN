<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\ItemRepositoryInterface;
use Amasty\MegaMenu\Model\Menu\Content\Resolver;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenu\Model\OptionSource\SubmenuType;
use Amasty\MegaMenu\Model\Provider\FieldsToHideProvider;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Level implements ModifierInterface
{
    /**
     * @var Category
     */
    private $entity;

    /**
     * @var int
     */
    private $parentId;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var SubcategoriesPosition
     */
    private $subcategoriesPosition;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var Subcategory
     */
    private $subcategory;

    /**
     * @var FieldsToHideProvider
     */
    private $fieldsToHideProvider;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        RequestInterface $request,
        ItemRepositoryInterface $itemRepository,
        SubcategoriesPosition $subcategoriesPosition,
        Subcategory $subcategory,
        FieldsToHideProvider $fieldsToHideProvider
    ) {
        $this->parentId = (int) $request->getParam('parent', 0);
        $this->moduleManager = $moduleManager;
        $this->subcategoriesPosition = $subcategoriesPosition;
        $this->itemRepository = $itemRepository;
        $this->fieldsToHideProvider = $fieldsToHideProvider;
        $this->subcategory = $subcategory;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->modifyPageBuilder($meta);
        $meta = $this->modifyLevel($meta);

        return $meta;
    }

    private function modifyLevel(array $meta): array
    {
        switch ($this->getCategoryLevel() <=> Subcategory::TOP_LEVEL) {
            case -1:
                $itemsToHide = $this->fieldsToHideProvider->getRootCategoryFields();
                $options = $this->subcategoriesPosition->toOptionArray();
                $switcherConfig = false;
                break;
            case 0:
                $itemsToHide = $this->fieldsToHideProvider->getMainCategoryFields();
                $options = $this->subcategoriesPosition->toOptionArray();
                $switcherConfig = true;
                if ($this->subcategory->isShowSubcategories($this->entity)) {
                    $meta = $this->unsetContentNotice($meta);
                }
                break;
            case 1:
                $parentCategory = $this->entity->getParentCategory();
                if ($parentCategory && $this->subcategory->isShowSubcategories($parentCategory)) {
                    $itemsToHide = $this->fieldsToHideProvider->getSubCategoryFields(true);
                    $meta = $this->unsetContentNotice($meta);
                } else {
                    $itemsToHide = $this->fieldsToHideProvider->getSubCategoryFields();
                }

                $options = $this->subcategoriesPosition->toOptionArray(true);
                $switcherConfig = false;
                break;
        }
        $this->updateItemsToHide($itemsToHide);

        return $this->updateMeta($meta, $switcherConfig, $options, array_unique($itemsToHide));
    }

    private function updateItemsToHide(array &$itemsToHide)
    {
        if (!$this->entity->hasChildren()) {
            $itemsToHide[] = ItemInterface::SUBCATEGORIES_POSITION;
            $itemsToHide[] = ItemInterface::SUBMENU_TYPE;
        }

        if ($this->entity->isObjectNew()) {
            $itemsToHide[] = ItemInterface::SUBMENU_TYPE;
        }
    }

    private function getCategoryLevel(): int
    {
        if ($this->parentId && $this->entity->isObjectNew()) {
            $this->entity->setParentId($this->parentId);
            $level = $this->entity->getParentCategory()->getLevel() + 1;
        } else {
            $level = $this->entity->getLevel();
        }

        return (int) $level;
    }

    private function unsetContentNotice(array $meta): array
    {
        unset(
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['notice']
        );
        unset(
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['default']
        );

        return $meta;
    }

    private function updateMeta(array $meta, bool $switcherConfig, array $options, array $itemsToHide): array
    {
        $fields = &$meta['am_mega_menu_fieldset']['children'];
        $fields['submenu_type']['arguments']['data']['config']['switcherConfig']['enabled'] = $switcherConfig;
        $fields['subcategories_position']['arguments']['data']['options'] = $options;

        foreach ($itemsToHide as $item) {
            $fields[$item]['arguments']['data']['config']['visible'] = false;
        }

        return $meta;
    }

    private function modifyPageBuilder(array $meta): array
    {
        $config = &$meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config'];
        if ($this->moduleManager->isEnabled('Magento_PageBuilder')) {
            if ($this->moduleManager->isEnabled('Amasty_MegaMenuPageBuilder')) {
                $config['default'] = Resolver::CHILD_CATEGORIES_PAGE_BUILDER;
                $config['notice'] = __('You can use the menu item Add Content for showing child categories.');
                $config['defaultNotice'] = $config['notice'];
            } else {
                $config['default'] = Resolver::CHILD_CATEGORIES;
            }
            $config['component'] = 'Amasty_MegaMenu/js/form/components/wysiwyg';
        } else {
            $config['default'] = Resolver::CHILD_CATEGORIES;
            $config['component'] = 'Amasty_MegaMenu/js/form/element/wysiwyg';
            $config['notice'] = __(
                'You can use the variable: {{child_categories_content}} for showing child categories.'
            );
            $config['defaultNotice'] = $config['notice'];
        }

        return $meta;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->entity = $category;
        return $this;
    }
}

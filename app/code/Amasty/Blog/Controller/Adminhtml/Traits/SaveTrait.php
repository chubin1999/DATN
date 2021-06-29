<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


declare(strict_types=1);

namespace Amasty\Blog\Controller\Adminhtml\Traits;

trait SaveTrait
{
    /**
     * @param array $data
     * @param $defaultStoreEntity
     * @return array
     */
    public function retrieveItemContent($data, $defaultStoreEntity)
    {
        $useDefaults = $this->getRequest()->getParam('use_default', []);
        $storeId = (int)$this->getRequest()->getParam('store_id', 0);

        if ($storeId) {
            foreach ($this->getFieldsByStore() as $fieldSet) {
                foreach ($fieldSet as $field) {
                    $this->setNullOnDefaultValues($data, $field, $defaultStoreEntity, $useDefaults);
                }
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param string $field
     * @param $defaultStoreCategory
     * @param array $useDefaults
     */
    public function setNullOnDefaultValues(&$data, $field, $defaultStoreCategory, $useDefaults)
    {
        if (isset($data[$field])) {
            $isEqualWithDefault = $data[$field] == $defaultStoreCategory->getData($field);
            if (isset($useDefaults[$field]) && ($useDefaults[$field] || $isEqualWithDefault)) {
                $data[$field] = null;
            }
        }
    }

    private function isUrlKeyExisted(?string $urlKey, ?int $id): bool
    {
        $isExist = false;
        if ($urlKey) {
            $entity = $this->getRepository()->getByUrlKey($urlKey);
            $isExist = $entity->getId() && (!$id || $entity->getId() != $id);
        }

        return $isExist;
    }

    public function addRedirect(int $id)
    {
        if ($id) {
            $this->_redirect('*/*/edit', ['id' => $id]);
        } else {
            $this->_redirect('*/*/new');
        }
    }
}

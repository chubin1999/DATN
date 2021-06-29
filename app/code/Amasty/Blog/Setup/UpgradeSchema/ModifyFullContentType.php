<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */

declare(strict_types=1);

namespace Amasty\Blog\Setup\UpgradeSchema;

use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Model\ResourceModel\Posts as PostResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class ModifyFullContentType
{
    public function execute(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $table = $setup->getTable(PostResource::TABLE_NAME);
        $connection->modifyColumn(
            $table,
            PostInterface::FULL_CONTENT,
            [
                'type' => Table::TYPE_TEXT,
                'length' => Table::MAX_TEXT_SIZE,
                'nullable' => false,
                'comment' => 'Full Content'
            ]
        );
    }
}

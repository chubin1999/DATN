<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


declare(strict_types=1);

namespace Amasty\Blog\Setup\Patch\Schema;

use Amasty\Blog\Api\Data\AuthorInterface;
use Amasty\Blog\Model\ResourceModel\Author;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddAdditionalAuthorData implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();
        $connection = $this->schemaSetup->getConnection();

        $connection->addColumn(
            $this->schemaSetup->getTable(Author::STORE_TABLE_NAME),
            AuthorInterface::JOB_TITLE,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Job Title',
            ]
        );
        $connection->addColumn(
            $this->schemaSetup->getTable(Author::TABLE_NAME),
            AuthorInterface::LINKEDIN_PROFILE,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Linkedin Profile',
            ]
        );
        $connection->addColumn(
            $this->schemaSetup->getTable(Author::STORE_TABLE_NAME),
            AuthorInterface::SHORT_DESCRIPTION,
            [
                'type' => Table::TYPE_TEXT,
                'length' => '2M',
                'nullable' => true,
                'comment' => 'Short Description',
            ]
        );
        $connection->addColumn(
            $this->schemaSetup->getTable(Author::STORE_TABLE_NAME),
            AuthorInterface::DESCRIPTION,
            [
                'type' => Table::TYPE_TEXT,
                'length' => '2M',
                'nullable' => true,
                'comment' => 'Description',
            ]
        );
        $connection->addColumn(
            $this->schemaSetup->getTable(Author::TABLE_NAME),
            AuthorInterface::IMAGE,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Image',
            ]
        );

        $this->schemaSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}

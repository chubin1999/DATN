<?php
namespace Margifox\MegaMenu\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();

        if(version_compare($context->getVersion(), '1.0.2', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable( 'amasty_menu_item_content' ),
                'customer_login',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            		null,
                    ['nullable' => false, 'default' => '1'],
                    'comment' => 'Add Customer Login'
                ]
            );
        }


        $installer->endSetup();
    }
}
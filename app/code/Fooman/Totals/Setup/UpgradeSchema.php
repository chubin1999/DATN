<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    private $tables = [
        'fooman_totals_quote_address',
        'fooman_totals_order',
        'fooman_totals_invoice',
        'fooman_totals_creditmemo'
    ];

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '3.0.0', '<')) {
            $this->addColumnLabel($setup);
            $this->addTaxColumns($setup);
            $this->addAmountColumns($setup);
        }
        if (version_compare($context->getVersion(), '9.0.0', '<')) {
            $this->addColumnQuoteBasePrice($setup);
        }
        if (version_compare($context->getVersion(), '9.1.1', '<')) {
            $this->addIndexToFoomanTotalsOrder($setup);
        }
        if (version_compare($context->getVersion(), '9.2.0', '<')) {
            $this->addIndexToMoreTables($setup);
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    private function addAmountColumns(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable('fooman_totals_order'),
            'amount_invoiced',
            [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length'  => '12,4',
                'comment' => 'Amount Invoiced',
                'after'   => 'base_tax_amount'
            ]
        );

        $connection->addColumn(
            $installer->getTable('fooman_totals_order'),
            'base_amount_invoiced',
            [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length'  => '12,4',
                'comment' => 'Base Amount Invoiced',
                'after'   => 'amount_invoiced'
            ]
        );

        $connection->addColumn(
            $installer->getTable('fooman_totals_order'),
            'amount_refunded',
            [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length'  => '12,4',
                'comment' => 'Amount Refunded',
                'after'   => 'base_amount_invoiced'
            ]
        );

        $connection->addColumn(
            $installer->getTable('fooman_totals_order'),
            'base_amount_refunded',
            [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length'  => '12,4',
                'comment' => 'Base Amount Refunded',
                'after'   => 'amount_refunded'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    private function addColumnLabel(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        foreach ($this->tables as $table) {
            $connection->addColumn(
                $installer->getTable($table),
                'label',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => 255,
                    'default'  => null,
                    'comment'  => 'Label',
                    'after'    => 'code'
                ]
            );
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    private function addTaxColumns(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        foreach ($this->tables as $table) {
            if ($table === 'fooman_totals_quote_address') {
                continue;
            }
            $connection->addColumn(
                $installer->getTable($table),
                'tax_amount',
                [
                    'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'  => '12,4',
                    'comment' => 'Tax Amount',
                    'after'   => 'base_amount'
                ]
            );

            $connection->addColumn(
                $installer->getTable($table),
                'base_tax_amount',
                [
                    'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'  => '12,4',
                    'comment' => 'Base Tax Amount',
                    'after'   => 'tax_amount'
                ]
            );
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    private function addColumnQuoteBasePrice(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable('fooman_totals_quote_address'),
            'base_price',
            [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length'  => '12,4',
                'comment' => 'Base Price',
                'after'   => 'base_amount'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    private function addIndexToFoomanTotalsOrder(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->addIndex(
            $installer->getTable('fooman_totals_order'),
            $installer->getIdxName('fooman_totals_order', ['order_id']),
            ['order_id']
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    private function addIndexToMoreTables(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->addIndex(
            $installer->getTable('fooman_totals_order'),
            $installer->getIdxName('fooman_totals_order', ['code']),
            ['code']
        );
        $connection->addIndex(
            $installer->getTable('fooman_totals_order'),
            $installer->getIdxName('fooman_totals_order', ['type_id']),
            ['type_id']
        );

        // Invoices
        $connection->addIndex(
            $installer->getTable('fooman_totals_invoice'),
            $installer->getIdxName('fooman_totals_invoice', ['order_id']),
            ['order_id']
        );
        $connection->addIndex(
            $installer->getTable('fooman_totals_invoice'),
            $installer->getIdxName('fooman_totals_invoice', ['invoice_id']),
            ['invoice_id']
        );
        $connection->addIndex(
            $installer->getTable('fooman_totals_invoice'),
            $installer->getIdxName('fooman_totals_invoice', ['code']),
            ['code']
        );
        $connection->addIndex(
            $installer->getTable('fooman_totals_invoice'),
            $installer->getIdxName('fooman_totals_invoice', ['type_id']),
            ['type_id']
        );

        // Creditmemo
        $connection->addIndex(
            $installer->getTable('fooman_totals_creditmemo'),
            $installer->getIdxName('fooman_totals_creditmemo', ['order_id']),
            ['order_id']
        );
        $connection->addIndex(
            $installer->getTable('fooman_totals_creditmemo'),
            $installer->getIdxName('fooman_totals_creditmemo', ['creditmemo_id']),
            ['creditmemo_id']
        );
        $connection->addIndex(
            $installer->getTable('fooman_totals_creditmemo'),
            $installer->getIdxName('fooman_totals_creditmemo', ['code']),
            ['code']
        );
        $connection->addIndex(
            $installer->getTable('fooman_totals_creditmemo'),
            $installer->getIdxName('fooman_totals_creditmemo', ['type_id']),
            ['type_id']
        );
    }
}

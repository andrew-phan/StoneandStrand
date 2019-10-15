<?php

namespace Ss\Designer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(SchemaSetupInterface $setup,
        ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.2.9') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'image_product', [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Image on product page',
            ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_type'), 'is_vintage', [
                'type' => Table::TYPE_SMALLINT,
                'length' => 2,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'Is Vintage Template',
            ]);
        }
        if (version_compare($context->getVersion(), '2.3.2') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'first_name', [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'First Name',
            ]);
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'last_name', [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Last Name',
            ]);
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'is_active', [
                'type' => Table::TYPE_SMALLINT,
                'length' => 2,
                'nullable' => true,
                'default' => '0',
                'comment' => 'Is Active',
            ]);
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'gender', [
                'type' => Table::TYPE_SMALLINT,
                'length' => 2,
                'unsigned' => true,
                'nullable' => true,
                'default' => null,
                'comment' => 'Gender',
            ]);
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'order_note', [
                'type' => Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'default' => null,
                'comment' => 'Order Note',
            ]);
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'payment_note', [
                'type' => Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'default' => null,
                'comment' => 'Payment Note',
            ]);
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'meta_keywords', [
                'type' => Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'default' => null,
                'comment' => 'Meta Keywords',
            ]);
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'meta_description', [
                'type' => Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'default' => null,
                'comment' => 'Meta Description',
            ]);
        }

        if (version_compare($context->getVersion(), '2.3.3') < 0) {
            $setup->getConnection()->dropColumn($setup->getTable('ss_designer_designer'), 'is_active');
        }

        if (version_compare($context->getVersion(), '2.3.5') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'option_id', [
                'type' => Table::TYPE_INTEGER,
                'length' => 10,
                'nullable' => true,
                'default' => null,
                'comment' => 'Option Id',
            ]);
        }

        if (version_compare($context->getVersion(), '2.3.6') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'is_active', [
                'type' => Table::TYPE_SMALLINT,
                'length' => 2,
                'nullable' => true,
                'default' => 1,
                'comment' => 'Is Active',
            ]);
        }


        if (version_compare($context->getVersion(), '2.3.9') < 0) {
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_designer_designer'), 
                    $setup->getIdxName('ss_designer_designer', ['type_id']),
                    ['type_id']
                );
            
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_designer_designer'), 
                    $setup->getIdxName('ss_designer_designer', ['designer_id']),
                    ['designer_id']
                );
        }


        if (version_compare($context->getVersion(), '2.4.0') < 0) {
            $setup->getConnection()
                ->dropIndex(
                    $setup->getTable('ss_designer_designer'), 
                    $setup->getIdxName('ss_designer_designer', ['designer_id'])
                );
            
            $setup->getConnection()
                ->dropIndex(
                    $setup->getTable('ss_designer_designer'), 
                    $setup->getIdxName('ss_designer_designer', ['type_id'])
                );
            
            $setup->getConnection()
                ->dropIndex(
                    $setup->getTable('ss_designer_tags'), 
                    $setup->getIdxName('ss_designer_tags', ['tag_id'])
                );
            
            $setup->getConnection()
                ->dropIndex(
                    $setup->getTable('ss_designer_type'), 
                    $setup->getIdxName('ss_designer_type', ['type_id'])
                );
            
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_designer_designer'), 
                    $setup->getIdxName('ss_designer_designer', ['name']),
                    ['name']
                );
            
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_designer_designer'), 
                    $setup->getIdxName('ss_designer_designer', ['is_active']),
                    ['is_active']
                );
            
            $setup->getConnection()->changeColumn(
                $setup->getTable('ss_designer_designer'), 'url_key', 'url_key', [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => '',
                'comment' => 'Designer url key',
            ]);
            
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_designer_designer'), 
                    $setup->getIdxName('ss_designer_designer', ['url_key']),
                    ['url_key'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                );
            
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_designer_type'), 
                    $setup->getIdxName('ss_designer_type', ['is_vintage']),
                    ['is_vintage']
                );
            
            $setup->getConnection()
                ->addForeignKey(
                    $setup->getFkName(
                        'ss_designer_designer',
                        'type_id',
                        'ss_designer_type',
                        'type_id'
                    ),
                    $setup->getTable('ss_designer_designer'),
                    'type_id',
                    $setup->getTable('ss_designer_type'),
                    'type_id'
                );
            
            $setup->getConnection()
                ->addForeignKey(
                    $setup->getFkName(
                        'ss_designer_designer_tag',
                        'designer_id',
                        'ss_designer_designer',
                        'designer_id'
                    ),
                    $setup->getTable('ss_designer_designer_tag'),
                    'designer_id',
                    $setup->getTable('ss_designer_designer'),
                    'designer_id'
                );
            
            $setup->getConnection()
                ->addForeignKey(
                    $setup->getFkName(
                        'ss_designer_designer_tag',
                        'tag_id',
                        'ss_designer_tags',
                        'tag_id'
                    ),
                    $setup->getTable('ss_designer_designer_tag'),
                    'tag_id',
                    $setup->getTable('ss_designer_tags'),
                    'tag_id'
                );
        }
        
        if (version_compare($context->getVersion(), '2.4.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'meta_title', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'default' => null,
                'comment' => 'Meta Title',
            ]);
        }
        
        if (version_compare($context->getVersion(), '2.4.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_designer_designer'), 'meta_title', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'default' => null,
                'comment' => 'Meta Title',
            ]);
        }
        
        $setup->endSetup();
    }

}

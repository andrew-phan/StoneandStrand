<?php

namespace Ss\Collection\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
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


        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_collection_collection'), 'is_active', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 2,
                'nullable' => true,
                'default' => 1,
                'comment' => 'Is Active',
            ]);
        }
        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ss_collection_collection'), 'url_menu', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Url Menu',
            ]);
        }
        
        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_collection_collection'), 
                    $setup->getIdxName('ss_collection_collection', ['collection_id']),
                    ['collection_id']
                );
            
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_collection_collection'), 
                    $setup->getIdxName('ss_collection_collection', ['option_id']),
                    ['option_id']
                );
        }
        
        if (version_compare($context->getVersion(), '2.0.4') < 0) {
            $setup->getConnection()
                ->dropIndex(
                    $setup->getTable('ss_collection_collection'), 
                    $setup->getIdxName('ss_collection_collection', ['collection_id'])
                );
            
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_collection_collection'), 
                    $setup->getIdxName('ss_collection_collection', ['is_active']),
                    ['is_active']
                );
            
            $setup->getConnection()->changeColumn(
                $setup->getTable('ss_collection_collection'), 'url_key', 'url_key', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => '',
                'comment' => 'Collection url key',
            ]);
            
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_collection_collection'), 
                    $setup->getIdxName('ss_collection_collection', ['url_key']),
                    ['url_key'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                );
        }
        
        $setup->endSetup();
    }

}

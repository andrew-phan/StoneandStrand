<?php

namespace Ss\Collection\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

class InstallSchema implements InstallSchemaInterface
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

    /**
     * install tables
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['upgrade' => $setup]);

        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'ss_collection');


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'ss_collection', [
            'type' => 'varchar',
            'label' => 'Collection',
            'input' => 'multiselect',
            'required' => false,
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'source' => 'Ss\Collection\Model\Config\Source\CollectionOptions',
            'visible' => true,
            'user_defined' => false,
            'searchable' => false,
            'filterable' => true,
            'filterable_in_search' => true,
            'comparable' => false,
            'visible_on_front' => false,
            'unique' => false,
            'group' => 'General',
            'is_used_in_grid' => true,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => true,
            ]
        );


        $installer = $setup;
        $installer->startSetup();

        /*
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable($installer->getTable('ss_collection_collection'));



        /*
         * Create table ss_collection_collection
         */
        $this->createTableCollection($installer);
        /*
         * End create table ss_collection_collection
         */

        $installer->endSetup();
    }

    /**
     *
     * @param type $installer
     * @return type
     */
    public function createTableCollection($installer)
    {
        $table = $installer->getConnection()->newTable(
                $installer->getTable('ss_collection_collection')
            )->addColumn(
                'collection_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true], 'Collection ID'
            )->addColumn(
                'option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['unsigned' => true,
                'nullable' => false], 'Option ID'
            )->addColumn(
                'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false,
                'default' => ''], 'Collection name'
            )->addColumn(
                'url_key', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['nullable' => false,
                'default' => ''], 'Collection url key'
            )->addColumn(
                'short_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [], 'Collection Quote'
            )->addColumn(
                'description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [], 'Collection Description'
            )->addColumn(
                'image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Collection image'
            )->addIndex(
            $installer->getIdxName('ss_collection_collection', ['collection_id']), ['collection_id']
        );
        $installer->getConnection()->createTable($table);
        return $installer;
    }

}


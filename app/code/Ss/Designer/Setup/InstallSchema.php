<?php

namespace Ss\Designer\Setup;

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
    public function install(SchemaSetupInterface $setup,
        ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['upgrade' => $setup]);

        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'ss_designer');

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'ss_designer', [
            'group' => 'General',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Designer',
            'input' => 'select',
            'class' => '',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'source' => 'Ss\Designer\Model\Config\Source\DesignerOptions',
            'visible' => true,
            'required' => true,
            'user_defined' => false,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
            ]
        );


        $installer = $setup;
        $installer->startSetup();

        /*
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable($installer->getTable('ss_designer_designer'));
        $installer->getConnection()->dropTable($installer->getTable('ss_designer_tags'));
        $installer->getConnection()->dropTable($installer->getTable('ss_designer_designer_tag'));
        $installer->getConnection()->dropTable($installer->getTable('ss_designer_type'));


        /*
         * Create table ss_designer_designer
         */
        $this->createTableSSDesignerDesigner($installer);
        /*
         * End create table ss_designer_designer
         */

        /*
         * Create table ss_designer_tag
         */
        $this->createTableSSDesignerTag($installer);
        /*
         * End create table ss_designer_tag
         */

        /*
         * Create table ss_designer_designer_tag
         */
        $this->createTableSSDesignerDesignerTag($installer);

        /*
         * End create table ss_designer_designer_tag
         */

        /*
         * Create table ss_designer_type
         */

        $this->createTableSSDesignerType($installer);

        /*
         * End create table ss_designer_tag
         */

        $installer->endSetup();
    }

    /**
     *
     * @param type $installer
     * @return type
     */
    public function createTableSSDesignerDesigner($installer)
    {
        $table = $installer->getConnection()->newTable(
                $installer->getTable('ss_designer_designer')
            )->addColumn(
                'designer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true], 'Designer ID'
            )->addColumn(
                'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false,
                'default' => ''], 'Designer name'
            )->addColumn(
                'url_key', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['nullable' => false,
                'default' => ''], 'Designer url key'
            )->addColumn(
                'type_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['unsigned' => true,
                'nullable' => false], 'Type ID'
            )->addColumn(
                'email', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['nullable' => true], 'Designer Email'
            )->addColumn(
                'address', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [], 'Designer Address'
            )->addColumn(
                'quote', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [], 'Designer Quote'
            )->addColumn(
                'why_love', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', ['default' => ''], 'Designer why love'
            )->addColumn(
                'description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [], 'Designer Description'
            )->addColumn(
                'image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Designer image'
            )->addIndex(
            $installer->getIdxName('ss_designer_designer', ['designer_id']), ['designer_id']
        );
        $installer->getConnection()->createTable($table);
        return $installer;
    }

    /**
     *
     * @param type $installer
     * @return type
     */
    public function createTableSSDesignerTag($installer)
    {
        $table = $installer->getConnection()->newTable(
                $installer->getTable('ss_designer_tags')
            )->addColumn(
                'tag_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true], 'Tag ID'
            )->addColumn(
                'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false,
                'default' => ''], 'Tag name'
            )->addColumn(
                'description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [], 'Tag Description'
            )->addIndex(
            $installer->getIdxName('ss_designer_tags', ['tag_id']), ['tag_id']
        );
        $installer->getConnection()->createTable($table);
        return $installer;
    }

    /**
     *
     * @param type $installer
     * @return type
     */
    public function createTableSSDesignerDesignerTag($installer)
    {
        $table = $installer->getConnection()->newTable(
                $installer->getTable('ss_designer_designer_tag')
            )->addColumn(
                'designer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['identity' => false,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true], 'Designer ID'
            )->addColumn(
            'tag_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['identity' => false,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true], 'Tag ID'
        );
        $installer->getConnection()->createTable($table);
        return $installer;
    }

    /**
     *
     * @param type $installer
     * @return type
     */
    public function createTableSSDesignerType($installer)
    {
        $table = $installer->getConnection()->newTable(
                $installer->getTable('ss_designer_type')
            )->addColumn(
                'type_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true], 'Type ID'
            )->addColumn(
                'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false,
                'default' => ''], 'Type name'
            )->addColumn(
                'description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [], 'Type Description'
            )->addIndex(
            $installer->getIdxName('ss_designer_type', ['type_id']), ['type_id']
        );
        $installer->getConnection()->createTable($table);

        return $installer;
    }

}

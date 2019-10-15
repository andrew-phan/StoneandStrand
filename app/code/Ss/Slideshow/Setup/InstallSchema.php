<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Bannerslider
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Ss\Slideshow\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Install schema
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /*
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable($installer->getTable('ss_slideshow_banner'));


        /*
         * Create table magestore_bannerslider_banner
         */
        $table = $installer->getConnection()->newTable(
                $installer->getTable('ss_slideshow_banner')
            )->addColumn(
                'banner_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true], 'Banner ID'
            )->addColumn(
                'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false,
                'default' => ''], 'Banner name'
            )->addColumn(
                'order_banner', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['nullable' => true,
                'default' => 0], 'Banner order'
            )->addColumn(
                'target_url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true,
                'default' => ''], 'Banner target'
            )->addColumn(
                'description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], 'Banner Description'
            )->addColumn(
                'image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], 'Banner image'
            )->addColumn(
                'image_alt', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], 'Banner image alt'
            )->addColumn(
                'style_slide', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], 'Banner style'
            )->addColumn(
                'width', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, 10, ['nullable' => true], 'Banner width'
            )->addColumn(
                'height', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, 10, ['nullable' => true], 'Banner height'
            )->addIndex(
            $installer->getIdxName('ss_slideshow_banner', ['banner_id']), ['banner_id']
        );
        $installer->getConnection()->createTable($table);
        /*
         * End create table magestore_bannerslider_banner
         */

        $installer->endSetup();
    }

}

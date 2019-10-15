<?php

namespace Ss\Slideshow\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

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

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.4') < 0) {
            $this->eavSetupFactory->create(['upgrade' => $setup]);

            $setup->getConnection()->addColumn($setup->getTable('ss_slideshow_banner'), 'banner_type', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => 'homepage',
                'comment' => 'Banner\'s type',
            ]);
        }
        
        if (version_compare($context->getVersion(), '2.0.5') < 0) {
            $setup->getConnection()
                ->addIndex(
                    $setup->getTable('ss_slideshow_banner'), 
                    $setup->getIdxName('ss_slideshow_banner', ['banner_type']),
                    ['banner_type']
                );
        }
        $setup->endSetup();

    }
}

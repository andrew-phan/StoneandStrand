<?php

namespace Ss\Checkout\Setup;

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

    public function upgrade(SchemaSetupInterface $setup,
        ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('salesrule_coupon'), 'message', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'default' => null,
                'comment' => 'message promotion',
            ]);
        }
        
        $setup->endSetup();
    }

}

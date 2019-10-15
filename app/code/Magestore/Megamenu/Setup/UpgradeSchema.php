<?php

/**
 * Magestore.
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
 * @package     Magestore_Megamenu
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Megamenu\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /*your code here*/
        
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            // Get module table
//            $tableName = $setup->getTable('magestore_megamenu_megamenu');

            // Check if the table already exists
//            if ($setup->getConnection()->isTableExists($tableName) == true) {
//                // Declare data
//                $columns = [
//                    'item_class' => [
//                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
//                        'nullable' => false,
//                        'comment' => 'Class item',
//                    ],
//                ];
//
//                $connection = $setup->getConnection();
//                foreach ($columns as $name => $definition) {
//                    $connection->addColumn($tableName, $name, $definition);
//                }
//
//            }
        }

        $setup->endSetup();
    }
}

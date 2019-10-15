<?php

/* app/code/Atwix/TestAttribute/Setup/InstallData.php */

namespace Ss\Designer\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $_typeFactory;

    /**
     * Init
     *
     */
    public function __construct(\Ss\Designer\Model\TypeFactory $typeFactory)
    {
        $this->_typeFactory = $typeFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup,
        ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableName = $setup->getTable('ss_designer_type');

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $data = [
                [
                    'name' => 'Vintage',
                    'description' => '',
                ],
                [
                    'name' => 'Modern',
                    'description' => '',
                ]
            ];

            // Insert data to table
            foreach ($data as $item) {
                $setup->getConnection()->insert($tableName, $item);
            }
        }

        $setup->endSetup();
    }

}

<?php

namespace Ss\Catalog\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetup;
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
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['upgrade' => $setup]);

        if (version_compare($context->getVersion(), '2.0.4') < 0) {
            $eavSetup->removeAttribute(
                'catalog_category', \Ss\Catalog\Helper\Data::ATTRIBUTE_IMAGE_MOBILE);

            $eavSetup->addAttribute(
                'catalog_category', \Ss\Catalog\Helper\Data::ATTRIBUTE_IMAGE_MOBILE, [
                'type' => 'varchar',
                'label' => 'Image mobile',
                'input' => 'image',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
                'used_in_product_listing' => true,
                'user_defined' => false,
                ]
            );
        }


        $setup->endSetup();
    }

}

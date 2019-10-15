<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Source;

/**
 * Class ProductAttribute
 *
 * @package Amasty\Sorting\Model\Source
 */
class ProductAttribute
{
    /**
     * @var \Amasty\Sorting\Helper\Method
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Amasty\Sorting\Helper\Method             $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Amasty\Sorting\Helper\Method $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_helper = $helper;
        $this->_objectManager = $objectManager;
    }


    /**
     * @return array
     */
    public function toOptionArray()
    {
        $attributes = $this->_objectManager->get('Magento\Eav\Model\Config')
            ->getEntityType(\Magento\Catalog\Model\Product::ENTITY)
            ->getAttributeCollection()
            ->addSetInfo();

        $options = array();
        foreach ($attributes as $item) {
            if ($item->getBackendType() == 'decimal') {
                $options[] = array(
                    'value' => __($item->getAttributeCode()),
                    'label' => __($item->getFrontendLabel()),
                );
            }
        }
        return $options;
    }
}
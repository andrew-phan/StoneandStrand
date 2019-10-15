<?php

namespace Ss\Designer\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

/**
 * Custom Attribute Renderer
 *
 * @author      Webkul Core Team <support@webkul.com>
 */
class Category extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {

        if ($this->_options) {
            return $this->_options;
        }

        $this->_options = $this->getAllCategories();

        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Custom Attribute Options  ' . $attributeCode . ' column',
            ],
        ];
    }

    public function getAllCategories()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Api\CategoryManagementInterface $obj */
        $obj = $objectManager->create('\Magento\Catalog\Api\CategoryManagementInterface');
        $rootId = \Ss\Theme\Helper\Data::ROOT_CATEGORY_ID;
        $depth = \Ss\Theme\Helper\Data::DEPTH_CATEGORY_RENDER;

        /** @var \Magento\Catalog\Api\Data\CategoryTreeInterface $items */
        $items = $obj->getTree($rootId, $depth);
        $html = [];
        foreach ($items->getChildrenData() as $rootItem) {
            // foreach children item.
            $html[] = ['label' => $rootItem->getName(),
                'value' => $rootItem->getId()];
            $html = array_merge($html, $this->getChildItemCategories($rootItem, '---'));
        }

        return $html;
    }

    public function getChildItemCategories($category,
        $prefix = '')
    {
        $html = [];
        foreach ($category->getChildrenData() as $childItem) {
            if ($childItem->getIsActive()) {
                $html[] = ['label' => $prefix . ' ' . $childItem->getName(),
                    'value' => $childItem->getId()];
                $html = array_merge($html, $this->getChildItemCategories($childItem, $prefix . '---'));
            }
        }

        return $html;
    }

}

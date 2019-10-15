<?php

namespace Ss\Designer\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

/**
 * Custom Attribute Renderer
 *
 * @author      Webkul Core Team <support@webkul.com>
 */
class DesignerOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    protected $_request;
    protected $_allowHandleDeactive = [
        "catalog_product_new",
        "catalog_product_edit"
    ];

    public function __construct(\Magento\Framework\App\Action\Context $context)
    {
        $this->_request = $context->getRequest();
    }

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
        $currentHandle = $this->_request->getFullActionName();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $designer = $objectManager->create('Ss\Designer\Model\ResourceModel\Designer\Collection');

        if (in_array($currentHandle, $this->_allowHandleDeactive)) {
            $designer->addFieldToFilter('is_active', ["IN" => 1]);
        }

        $this->_options = [[
            'label' => 'Select Designer',
            'value' => '']];
        foreach ($designer as $item) {
            $this->_options[] = ['label' => $item->getName(),
                'value' => $item->getDesignerId()];
        }

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

}

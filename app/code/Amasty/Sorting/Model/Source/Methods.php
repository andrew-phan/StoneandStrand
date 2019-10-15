<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Source;

/**
 * Class Methods
 *
 * @package Amasty\Sorting\Model\Source
 */
class Methods implements \Magento\Framework\Option\ArrayInterface
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
        $options = [];

        foreach ($this->_helper->getMethods(true) as $method) {
            $options[] = [
                'value' => $method['code'],
                'label' => __($method['name'])
            ];
        }

        return $options;
    }
}
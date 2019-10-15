<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Source;

/**
 * Class State
 *
 * @package Amasty\Sorting\Model\Source
 */
class State implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        $states = $this->_objectManager->get('Magento\Sales\Model\Order\Status')
            ->getCollection()->load();

        foreach ($states as $state => $node) {
            $label = __(trim((string)$node->getLabel()));
            $options[] = ['value' => $state, 'label' => $label];
        }

        return $options;
    }
}
<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sales order view items block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Ss\Sales\Block\Order;

class Items extends \Magento\Sales\Block\Order\Items
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_themeHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ss\Theme\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_themeHelper = $helper;
        parent::__construct($context, $registry, $data);
    }
    
    /**
     * Get Theme helper
     * @return type
     */
    public function getThemeHelper()
    {
        return $this->_themeHelper;
    }
}

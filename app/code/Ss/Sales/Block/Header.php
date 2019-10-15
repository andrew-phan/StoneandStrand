<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Sales\Block;

/**
 * Class Header
 */
class Header extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'header.phtml';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_themeHelper;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Ss\Theme\Helper\Data $helper, array $data = [])
    {
        $this->_themeHelper = $helper;
        parent::__construct($context, $data);
    }
    
    /**
     * Get Theme helper
     * @return type
     */
    public function getThemeHelper()
    {
        return $this->_themeHelper;
    }
    
    /**
     * Get Title
     * @return type
     */
    public function getTitle()
    {
        return $this->getData('title');
    }
}

<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Contact\Block\Widget;

/**
 * Main contact form block
 */
class ContactForm extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    protected $_storeManager;
    protected $_themeHelper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Ss\Contact\Helper\Data $helper, array $data = [])
    {
        $this->_themeHelper = $helper;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);

    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate($this->getTemplateName());

    }

    /**
     * get StoreManager
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    public function getTemplateName()
    {
        return !empty($this->getData('template')) ? $this->getData('template') : 'widget/contact/nut_shell.phtml';

    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('contact/index/post', ['_secure' => true]);

    }
    
    /**
     * Get Title
     * @return string
     */
    public function getTitle(){
        return $this->getData('title');
    }
    
    /**
     * Get Header Info
     * @return string
     */
    public function getHeaderInfo(){
        return $this->getData('header_info');
    }
}

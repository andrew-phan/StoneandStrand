<?php

namespace Ss\Contact\Block\Widget;

/**
 * Banner Widget Block
 *
 */
class Nutshell extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    protected $_storeManager;
    protected $_themeHelper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Ss\Theme\Helper\Data $helper
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Ss\Theme\Helper\Data $helper, array $data = [])
    {
        $this->_themeHelper = $helper;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);

    }

    /**
     * construct to set tempalte
     */
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
    function getStoreManager()
    {
        return $this->_storeManager;

    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();

    }

    /**
     *
     * @return Ss\Theme\Helper\Data
     */
    function getThemeHelper()
    {
        return $this->_themeHelper;

    }

    public function getTemplateName()
    {
        return !empty($this->getData('template')) ? $this->getData('template') : 'widget/contact/nut_shell.phtml';

    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');

    }

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getData('email');

    }

    /**
     * Get Phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->getData('phone');

    }

    /**
     * Get Link URL
     *
     * @return String
     */
    public function getLink()
    {
        return $this->getData('link');

    }
}

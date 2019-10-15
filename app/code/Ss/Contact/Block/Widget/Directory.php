<?php

namespace Ss\Contact\Block\Widget;

/**
 * Banner Widget Block
 *
 */
class Directory extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    protected $_storeManager;
    protected $_helper;
    protected $_themeHelper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Ss\Contact\Helper\Data $helper, \Ss\Theme\Helper\Data $themeHelper, array $data = [])
    {
        $this->_helper = $helper;
        $this->_themeHelper = $themeHelper;
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

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();

    }

    /**
     *
     * @return Ss\Contact\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;

    }
    
    /**
     *
     * @return Ss\Theme\Helper\Data
     */
    public function getThemeHelper()
    {
        return $this->_themeHelper;

    }

    public function getTemplateName()
    {
        return !empty($this->getData('template')) ? $this->getData('template') : 'widget/contact/directory.phtml';

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
}

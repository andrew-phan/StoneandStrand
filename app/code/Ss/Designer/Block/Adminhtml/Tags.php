<?php

namespace Ss\Designer\Block\Adminhtml;

/**
 * Banner grid container.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Tags extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_tags';
        $this->_blockGroup = 'Ss_Designer';
        $this->_headerText = __('Tags');
        $this->_addButtonLabel = __('Add New Tag');
        parent::_construct();
    }

}

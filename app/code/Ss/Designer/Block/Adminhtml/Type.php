<?php

namespace Ss\Designer\Block\Adminhtml;

/**
 * Banner grid container.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Type extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_type';
        $this->_blockGroup = 'Ss_Designer';
        $this->_headerText = __('Type');
        $this->_addButtonLabel = __('Add New Type');
        parent::_construct();
    }

}

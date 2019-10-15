<?php

namespace Ss\Designer\Block\Adminhtml;

/**
 * Banner grid container.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Designer extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_designer';
        $this->_blockGroup = 'Ss_Designer';
        $this->_headerText = __('Designers');
        $this->_addButtonLabel = __('Add New Designer');
        parent::_construct();
    }

}

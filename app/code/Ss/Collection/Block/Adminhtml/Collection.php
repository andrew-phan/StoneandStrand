<?php

namespace Ss\Collection\Block\Adminhtml;

/**
 * Banner grid container.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Collection extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_collection';
        $this->_blockGroup = 'Ss_Collection';
        $this->_headerText = __('Collections');
        $this->_addButtonLabel = __('Add New Collection');
        parent::_construct();
    }

}

<?php

/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */

namespace Ss\ProductNew\Helper;

/**
 * Designer helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const DEFAULT_PATH_ALIAS = "new";      // Default alias page    
    const ATTRIBUTE_NEW_PRODUCT = "created_at"; // Attribute to sort
    const PARAM_DEFAULT_SORT_NEW = "desc";       // default sort on product new
    const TITLE_PAGE_NEW = "Product New";       // Title page product new
    const PARAM_SORT_DIRECTION = "product_list_dir";    // param default magento
    const PARAM_SORT_ORDER = "product_list_order";    // param default magento
    
    protected $_scopeConfig;

    /**
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        
    }

    /**
     * @todo To get attribute new from  config amsty_sorting
     */
    public function getConfigAttributeNew(){
        return $this->_scopeConfig->getValue('amsorting/new/new_attr', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}

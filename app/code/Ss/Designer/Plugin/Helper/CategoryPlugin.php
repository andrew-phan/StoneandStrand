<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Plugin\Helper;

/**
 * Class ProductPlugin
 * Magento\Catalog\Model\Product
 */
class CategoryPlugin
{

    protected $_request;

    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(\Magento\Framework\App\Action\Context $context)
    {
        $this->_request = $context->getRequest();
    }

    /*
     * @todo Check if this page is designer page then remove canonical of cateogry
     */
    public function aroundCanUseCanonicalTag(\Magento\Catalog\Helper\Category $subject,
        \Closure $proceed,
        $store = null)
    {
        $isDesignerPage = $this->_request->getParam(\Ss\Theme\Helper\Data::IS_FILTER_DESIGNER_PAGE, FALSE);
        if ($isDesignerPage) {
            return FALSE;
        }
        return $proceed($store);
    }

}

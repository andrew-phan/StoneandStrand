<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile

namespace Ss\Theme\Block\Product\ProductList;

/**
 * Catalog product related items block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Related extends \Magento\Catalog\Block\Product\ProductList\Related
{

    protected $_designerHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\Module\Manager $moduleManager, \Ss\Designer\Helper\Data $designerHelper, array $data = []
    )
    {
        parent::__construct(
            $context, $checkoutCart, $catalogProductVisibility, $checkoutSession, $moduleManager, $data
        );

        $this->_designerHelper = $designerHelper;
    }

    /**
     * @todo to get designer helper.
     * @return type
     */
    public function getDesignerHelper()
    {
        return $this->_designerHelper;
    }

}

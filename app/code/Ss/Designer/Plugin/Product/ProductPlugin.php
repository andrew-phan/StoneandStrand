<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Plugin\Product;

/**
 * Class ProductPlugin
 * Magento\Catalog\Model\Product
 */
class ProductPlugin
{

    protected $_designerFactory;
    protected $_request;
    protected $_themeHelper;
    protected $_storeManager;

    /**
     * 
     * @param \Ss\Designer\Model\DesignerFactory $designerFactory
     */
    public function __construct(
    \Ss\Designer\Model\DesignerFactory $designerFactory,
        \Magento\Framework\App\Action\Context $context,
        \Ss\Theme\Helper\Data $themeHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_designerFactory = $designerFactory;
        $this->_request = $context->getRequest();
        $this->_themeHelper = $themeHelper;
        $this->_storeManager = $storeManager;
    }

    /**
     * @todo To change url product 
     * 
     * @param \Magento\Catalog\Model\Product $subject
     * @param \Closure $proceed
     * @return type
     */
    public function aroundGetProductUrl(\Magento\Catalog\Model\Product $product,
        \Closure $proceed)
    {
        $attributeSetDiamond = $this->_themeHelper->getConfigAttributeSetDiamond();
        $isDesignerUrl = $this->_request->getParam(\Ss\Theme\Helper\Data::IS_FILTER_DESIGNER_PAGE, FALSE);
        
        if (($isDesignerUrl || $product->hasData(\Ss\Designer\Model\Designer::GET_URL_DESIGNER)) && $product->getSsDesigner()) {
            $designerId = $product->getSsDesigner();
            $designer = $this->_designerFactory->create()->load($designerId);
            $returnValue = $designer->getUrl() . '/' . $product->getUrlKey();
        } elseif ($product->getAttributeSetId() == $attributeSetDiamond) {
            $returnValue = $this->_storeManager->getStore()->getBaseUrl() . \Ss\Theme\Helper\Data::PREFIX_URL_REWRITE_DIAMONDS . $product->getUrlKey();
        } else {
            $returnValue = $proceed();
        }
        return $returnValue;
    }

}

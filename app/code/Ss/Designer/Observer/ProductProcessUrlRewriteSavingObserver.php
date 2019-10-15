<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Observer;

use Magento\Catalog\Model\Product\Visibility;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Observer event catalog_product_save_after
 */
class ProductProcessUrlRewriteSavingObserver implements ObserverInterface
{

    /**
     * @var UrlPersistInterface
     */
    protected $_designer;
    protected $_urlRewriteHelper;

    /**
     * 
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param \Ss\Designer\Model\Designer $designerFactory
     * @param \Ss\Designer\Helper\UrlRewrite $urlRewriteHelper
     */
    public function __construct(
    \Ss\Designer\Model\Designer $designerFactory,
        \Ss\Designer\Helper\SsUrlRewriteProduct $urlRewriteHelper
    )
    {
        $this->_designer = $designerFactory;
        $this->_urlRewriteHelper = $urlRewriteHelper;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();
        $designerId = $product->getSsDesigner();
        if (!empty($designerId)) {
            $this->_designer->load($designerId);

            if ($product->getVisibility() != Visibility::VISIBILITY_NOT_VISIBLE && $this->_designer->getIsActive()) {
                $this->_urlRewriteHelper->setProduct($product);
                $this->_urlRewriteHelper->setPrefixUrlDesigner($this->_designer->getUrlPath());
                $this->_urlRewriteHelper->generateUrlRewrite();
            }
        }
    }

}

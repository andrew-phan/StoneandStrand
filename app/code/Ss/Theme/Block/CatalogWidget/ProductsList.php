<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile

namespace Ss\Theme\Block\CatalogWidget;

/**
 * Catalog Products List widget block
 * Class ProductsList
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{

    protected $_designerFactory;
    protected $_stockState;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, \Magento\Framework\App\Http\Context $httpContext, \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder, \Magento\CatalogWidget\Model\Rule $rule, \Magento\Widget\Helper\Conditions $conditionsHelper, \Ss\Designer\Model\DesignerFactory $designerFatory, \Magento\CatalogInventory\Api\StockStateInterface $stockState, array $data = []
    )
    {
        $this->_designerFactory = $designerFatory;
        $this->_stockState = $stockState;
        parent::__construct(
            $context, $productCollectionFactory, $catalogProductVisibility, $httpContext, $sqlBuilder, $rule, $conditionsHelper, $data
        );
    }

    /**
     * @todo to get designer by id
     * @param type $designerId
     * @return boolean
     */
    public function getDesignerById($designerId)
    {
        if (!isset($designerId) || empty($designerId) || is_null($designerId)) {
            return FALSE;
        }

        $designer = $this->_designerFactory->create()->load($designerId);
        return ($designer->getIsActive()) ? $designer : FALSE;
    }

    /**
     * @todo to get template widget
     * @return type
     */
    public function getTemplateWidget()
    {
        return $this->getData('template');
    }

    /**
     * Get Link
     * @return string
     */
    public function getLink()
    {
        $url = $this->getData('link');
        return $url;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $conditions = $this->getData('conditions') ? $this->getData('conditions') : $this->getData('conditions_encoded');
        $template = $this->getTemplateWidget();
        $template = explode('/', $template);
        $name = array_pop($template);

        return [
            'CATALOG_PRODUCTS_LIST_WIDGET_' . $name,
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            intval($this->getRequest()->getParam(static::PAGE_VAR_NAME, 1)),
            $this->getProductsPerPage(),
            $conditions
        ];
    }

    public function getQtyProduct($product)
    {
        return $this->_stockState->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
    }

}

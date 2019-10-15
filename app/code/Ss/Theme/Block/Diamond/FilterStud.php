<?php

namespace Ss\Theme\Block\Diamond;

/**
 * @block diamond filter stuc
 */
class FilterStud extends \Magento\Catalog\Block\Product\AbstractProduct
{

    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';
    protected $_defaultPagerBlock = 'Magento\Theme\Block\Html\Pager';
    protected $_collectionProductFactory;
    protected $_catalogProductVisibility;
    protected $_themeHelper;
    protected $_collectionDiamond;
    protected $_request;

    /**
     * Construct
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Designer\Model\Designer $designer
     * @param \Ss\Designer\Model\DesignerFactory $designerFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionProductFactory, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, \Ss\Theme\Helper\Data $themeHelper, \Magento\Framework\App\Request\Http $request, array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_collectionProductFactory = $collectionProductFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_themeHelper = $themeHelper;
        $this->_request = $request;
    }

    public function getAttributeSetDiamond()
    {
        return $this->_themeHelper->getConfigAttributeSetDiamond();
    }

    /**
     * @todo init param to filter diamon product
     */
    public function initParamsFilter()
    {
        $delimiter = \Ss\Theme\Helper\Diamond::ATTRIBUTE_DELIMITER;
        $params = [
            \Ss\Theme\Helper\Diamond::ATTRIBUTE_SHAPE,
            \Ss\Theme\Helper\Diamond::ATTRIBUTE_SIZE,
            \Ss\Theme\Helper\Diamond::ATTRIBUTE_METAL_COLOR,
            \Ss\Theme\Helper\Diamond::ATTRIBUTE_STONE_COLOR
        ];

        foreach ($params as $attributeCode) {
            $value = $this->_request->getParam($attributeCode, '');
            if (!empty($value)) {
                $this->_collectionDiamond->addAttributeToFilter($attributeCode, ['IN', explode($delimiter, $value)]);
            }
        }
    }

    /**
     * @todo to creare and filter product diamond
     * @return type
     */
    public function createDiamondCollection()
    {
        $attributeSetDiamond = $this->getAttributeSetDiamond();
        
        $attributes = $this->_catalogConfig->getProductAttributes();
        
        $collection = $this->_collectionProductFactory->create();
        $this->_collectionDiamond = $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
            ->addAttributeToFilter('attribute_set_id', $attributeSetDiamond)
            ->addAttributeToSelect($attributes)
        ;

        $this->initParamsFilter();

        return $this->_collectionDiamond;
    }

    /**
     * @todo to get diamond product collection
     * @return type
     */
    public function getDiamondCollection()
    {
        return $this->_collectionDiamond;
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->createDiamondCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->_collectionDiamond = $toolbar->getCollection();

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection', ['collection' => $collection]
        );

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $blockPager = $this->getLayout()->createBlock($this->_defaultPagerBlock, uniqid(microtime()));
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        $block->setChild('product_list_toolbar_pager', $blockPager);
        return $block;
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

}

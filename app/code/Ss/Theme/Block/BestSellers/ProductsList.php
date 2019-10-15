<?php

namespace Ss\Theme\Block\BestSellers;

/**
 * Bestseller product list
 */
class ProductsList extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     *
     * @var \Magento\Reports\Model\ResourceModel\Report\Collection\Factory
     */
    protected $_resourceFactory;

    /**
     *
     * @var \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory  $productFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory, \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory, \Magento\Reports\Helper\Data $reportsData, array $data = []
    )
    {
        $this->_resourceFactory = $resourceFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_productFactory = $productFactory;
        $this->_reportsData = $reportsData;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate($this->getTemplateWidget());
    }

    /**
     * get featured product collection
     */
    public function getBestsellerProduct()
    {
        $limit = $this->getProductLimit();

        /**
         * @Todo: Fixbug bestsellers
         *
         * $resourceCollection = $this->_resourceFactory->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection');
         * $resourceCollection->setPageSize($limit);
         *  return $resourceCollection;
         *
         */
        $collection = $this->_collectionFactory->create()->setModel(
                        'Magento\Catalog\Model\Product'
                )->setPageSize($limit);
        return $collection;
    }

    /**
     * Load product by Id
     *
     * @param int $id
     * @return object|boolean
     */
    public function loadProductById($id)
    {
        $product = null;
        if ($id) {
            $product = $this->_productFactory->create()->load($id);
        }
        return $product;
    }

    /**
     * Get the configured limit of products
     * @return int
     */
    public function getProductLimit()
    {
        if ($this->getData('product_count') == '') {
            return static::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->getData('product_count');
    }

    public function getTemplateWidget()
    {
        return $this->getData('template');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Get Allshop URL
     * @return string
     */
    public function getAllShopUrl()
    {
        return $this->getData('shopall_url');
    }

    /**
     * Get Bestseller URL
     * @return string
     */
    public function getBestsellerUrl()
    {
        return $this->getData('bestseller_url');
    }

}

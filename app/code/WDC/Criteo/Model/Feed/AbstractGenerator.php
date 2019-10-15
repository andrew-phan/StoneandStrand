<?php

namespace WDC\Criteo\Model\Feed;

use Magento\Framework\DataObject;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogInventory\Model\Stock\ItemFactory as StockItemFactory;
use Magento\CatalogInventory\Model\Stock\Item as StockItem;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Ss\Designer\Model\DesignerFactory;
use Ss\Designer\Model\Designer;
use WDC\Criteo\Api\Feed\GeneratorInterface;

abstract class AbstractGenerator
    implements GeneratorInterface
{
    const WEBSITE_ID = 1;
    const CURRENCY_CODE = 'USD';
    const GENDER = 'Female';

    protected $_products;
    protected $_categoryCache = [];
    protected $_designerCache = [];
    protected $_productAttributes;
    protected $_productCollectionFactory;
    protected $_categoryCollectionFactory;
    protected $_stockItemFactory;
    protected $_designerFactory;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        StockItemFactory $stockItemFactory,
        DesignerFactory $designerFactory
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_designerFactory = $designerFactory;
        $this->_stockItemFactory = $stockItemFactory;
    }

    /**
     * @return ProductCollection|Product[]|null
     */
    public function getProducts()
    {
        if (!isset($this->_products)) {
            /** @var ProductCollection $products */
            $this->_products = $this->_productCollectionFactory->create();
            $this->_products->addAttributeToFilter('is_in_criteo_feed', 1)
                ->addAttributeToFilter('status', Product\Attribute\Source\Status::STATUS_ENABLED)
                ->addAttributeToFilter('visibility', ['neq' => Product\Visibility::VISIBILITY_NOT_VISIBLE])
                ->addAttributeToSelect($this->getProductAttributes());
        }
        return $this->_products;
    }

    /**
     * @param ProductCollection|Product[]|null $products
     * @return $this
     */
    public function setProducts($products)
    {
        $this->_products = $products;
        return $this;
    }

    /**
     * @return array
     */
    public function getProductAttributes()
    {
        if (!isset($this->_productAttributes)) {
            $this->_productAttributes = [
                'google_id',
                'designer_id',
                'url_key',
                'image',
                'price',
                'material',
                'color',
                'gtin',
                'mpn',
                'custom_label0',
                'promotion_id'
            ];
        }
        return $this->_productAttributes;
    }

    protected function _toArray()
    {
        $data = [];
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($this->getProducts() as $product) {
            /** @var StockItem $stockItem */
            $stockItem = $this->_stockItemFactory->create();
            $stockItem->getResource()->loadByProductId($stockItem, $product->getId(), self::WEBSITE_ID);
            $category = $this->_getGoogleProductCategory($product);
            /** @var \Ss\Designer\Model\Designer $designer */
            $designer = $this->_getDesigner($product->getData('designer_id'));
            $data[] = [
                'GOOGLE ID' => $product->getData('google_id'),
                'CRITEO ID' => $product->getId(),
                'BRAND' => $designer->getName(),
                'TITLE' => $product->getName(),
                'DESCRIPTION' => $product->getData('description'),
                'GOOGLE_PRODUCT_CATEGORY' => $category->getData('google_product_category'),
                'PRODUCT_TYPE' => $category->getName(),
                'LINK' => $product->getProductUrl(),
                'IMAGE_LINK' => $product->getImage(),
                'AGE GROUP' => 'Adult',
                'CONDITION' => 'New',
                'PRICE' => $product->getPrice() . ' ' . self::CURRENCY_CODE,
                'MATERIAL' => $product->getData('material'),
                'COLOR' => $product->getData('color'),
                'GENDER' => self::GENDER,
                'AVAILABILITY' => $stockItem->getIsInStock()? 'In Stock' : 'Out Of Stock',
                'identifier_exists' => ($product->getData('gtin') || $product->getData('mpn'))? 'TRUE' : 'FALSE',
                'GTIN' => $product->getData('gtin'),
                'custom_label_0' => $product->getData('custom_label0'),
                'promotion_id' => $product->getData('promotion_id'),
                'mpn' => $product->getData('mpn')
            ];
        }
        return $data;
    }

    /**
     * @param Product $product
     * @return Category|null
     */
    protected function _getGoogleProductCategory($product)
    {
        $categoryIds = $product->getCategoryIds();
        $this->_loadCategories($categoryIds);
        $maxArrows = 0;
        $bestCategory = null;
        foreach ($categoryIds as $categoryId) {
            /** @var Category $category */
            $category = $this->_categoryCache[$categoryId];
            if ($category->getIsActive()) {
                $cnt = substr_count($category->getData('google_product_category'), '>');
                if ($cnt > $maxArrows) {
                    $bestCategory = $category;
                    $maxArrows = $cnt;
                }
            }
        }
        return $bestCategory;
    }

    protected function _loadCategories($categoryIds)
    {
        $categoryIds = array_diff($categoryIds, array_keys($this->_categoryCache));
        if (!empty($categoryIds)) {
            /** @var CategoryCollection $categories */
            $categories = $this->_categoryCollectionFactory->create();
            $categories->addFieldToFilter('entity_id', ['in' => $categoryIds])
                ->addAttributeToSelect('google_product_category');
            foreach ($categories as $category) {
                $this->_categoryCache[$category->getId()] = $category;
            }
        }
    }

    protected function _getDesigner($designerId)
    {
        if (!isset($this->_designerCache[$designerId])) {
            $this->_designerCache[$designerId] = $this->_designerFactory->create()->load($designerId);
        }
        return $this->_designerCache[$designerId];
    }
}
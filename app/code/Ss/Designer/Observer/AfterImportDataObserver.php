<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Observer;

use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\Framework\Event\Observer;
use Magento\ImportExport\Model\Import as ImportExport;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AfterImportDataObserver
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AfterImportDataObserver implements ObserverInterface
{

    /**
     * Url Key Attribute
     */
    const URL_KEY_ATTRIBUTE_CODE = 'url_key';

    /** @var \Magento\CatalogUrlRewrite\Service\V1\StoreViewService */
    protected $storeViewService;

    /** @var \Magento\Catalog\Model\Product */
    protected $product;

    /** @var array */
    protected $productsWithStores;

    /** @var array */
    protected $products = [];

    /** @var \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory */
    protected $objectRegistryFactory;

    /** @var UrlFinderInterface */
    protected $urlFinder;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var UrlRewriteFactory */
    protected $urlRewriteFactory;

    /** @var \Magento\CatalogImportExport\Model\Import\Product */
    protected $import;

    /** @var \Magento\Catalog\Model\ProductFactory $catalogProductFactory */
    protected $catalogProductFactory;

    /** @var array */
    protected $acceptableCategories;

    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var array */
    protected $websitesToStoreIds;

    /** @var array */
    protected $storesCache = [];

    /** @var array */
    protected $categoryCache = [];

    /** @var array */
    protected $websiteCache = [];

    /** @var array */
    protected $vitalForGenerationFields = [
        'sku',
        'url_key',
        'url_path',
        'name',
    ];
    protected $_designerFactory;
    protected $_designers;
    protected $_urlRewriteHelper;
    protected $_urls;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $catalogProductFactory
     * @param \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory $objectRegistryFactory
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UrlPersistInterface $urlPersist
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param UrlFinderInterface $urlFinder
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    \Magento\Catalog\Model\ProductFactory $catalogProductFactory,
        \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory $objectRegistryFactory,
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator,
        \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlPersistInterface $urlPersist,
        UrlRewriteFactory $urlRewriteFactory,
        UrlFinderInterface $urlFinder,
        \Ss\Designer\Model\DesignerFactory $designerFactory,
        \Ss\Designer\Helper\SsUrlRewriteProduct $urlRewriteHelper
    )
    {
        $this->urlPersist = $urlPersist;
        $this->catalogProductFactory = $catalogProductFactory;
        $this->objectRegistryFactory = $objectRegistryFactory;
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->storeViewService = $storeViewService;
        $this->storeManager = $storeManager;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlFinder = $urlFinder;
        $this->_designerFactory = $designerFactory;
        $this->_designers = [];
        $this->_urls = [];
        $this->_urlRewriteHelper = $urlRewriteHelper;
    }

    /**
     * Action after data import.
     * Save new url rewrites and remove old if exist.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->import = $observer->getEvent()->getAdapter();
        if ($products = $observer->getEvent()->getBunch()) {
            foreach ($products as $product) {
                $this->_populateForUrlGeneration($product);
            }

            $productUrls = $this->generateUrls();
            $this->_urlRewriteHelper->insertMultiple($productUrls);
        }
    }

    /**
     * Create product model from imported data for URL rewrite purposes.
     *
     * @param array $rowData
     *
     * @return ImportExport
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _populateForUrlGeneration($rowData)
    {
        $newSku = $this->import->getNewSku($rowData[ImportProduct::COL_SKU]);
        if (empty($newSku) || !isset($newSku['entity_id'])) {
            return null;
        }
        if ($this->import->getRowScope($rowData) == ImportProduct::SCOPE_STORE && empty($rowData[self::URL_KEY_ATTRIBUTE_CODE])) {
            return null;
        }
        $rowData['entity_id'] = $newSku['entity_id'];

        $product = $this->catalogProductFactory->create();
        $product->setId($rowData['entity_id']);

        // Get Designer by name
        $designerName = $rowData[\Ss\Designer\Model\Designer::ATTRIBUTE_CODE];
        if (!isset($this->_designers[$designerName])) {
            $designerId = $this->_designerFactory->create()->getDesignerByName($designerName);
            if ($designerId && !empty($designerId)) {
                $this->_designers[$designerName] = $this->_designerFactory->create()->load($designerId);
            }
        }
        $product->setData(\Ss\Designer\Model\Designer::ATTRIBUTE_CODE, $designerName);


        foreach ($this->vitalForGenerationFields as $field) {
            if (isset($rowData[$field])) {
                $product->setData($field, $rowData[$field]);
            }
        }

        $this->websiteCache[$rowData['entity_id']] = $this->import->getProductWebsites($rowData['sku']);
        foreach ($this->websiteCache[$rowData['entity_id']] as $websiteId) {
            if (!isset($this->websitesToStoreIds[$websiteId])) {
                $this->websitesToStoreIds[$websiteId] = $this->storeManager->getWebsite($websiteId)->getStoreIds();
            }
        }

        $this->setStoreToProduct($product, $rowData);

        if ($this->isGlobalScope($product->getStoreId())) {
            $this->populateGlobalProduct($product);
        } else {
            $this->addProductToImport($product, $product->getStoreId());
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $rowData
     * @return void
     */
    protected function setStoreToProduct(\Magento\Catalog\Model\Product $product,
        array $rowData)
    {
        if (!empty($rowData[ImportProduct::COL_STORE]) && ($storeId = $this->import->getStoreIdByCode($rowData[ImportProduct::COL_STORE]))
        ) {
            $product->setStoreId($storeId);
        } elseif (!$product->hasData(\Magento\Catalog\Model\Product::STORE_ID)) {
            $product->setStoreId(Store::DEFAULT_STORE_ID);
        }
    }

    /**
     * Add product to import
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $storeId
     * @return $this
     */
    protected function addProductToImport($product,
        $storeId)
    {
        if (!isset($this->products[$product->getId()])) {
            $this->products[$product->getId()] = [];
        }
        $this->products[$product->getId()][$storeId] = $product;
        return $this;
    }

    /**
     * Populate global product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    protected function populateGlobalProduct($product)
    {
        foreach ($this->import->getProductWebsites($product->getSku()) as $websiteId) {
            foreach ($this->websitesToStoreIds[$websiteId] as $storeId) {
                $this->storesCache[$storeId] = true;
                if (!$this->isGlobalScope($storeId)) {
                    $this->addProductToImport($product, $storeId);
                }
            }
        }
        return $this;
    }

    /**
     * Generate product url rewrites
     *
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateUrls()
    {
        /* Reduce duplicates. Last wins */
        $results = $this->designerUrlRewriteGenerate();

        $this->products = [];
        $this->_designers = [];
        return $results;
    }

    /**
     * @todo To generate url rewrite
     * @return type
     */
    protected function designerUrlRewriteGenerate()
    {
        $urls = [];
        foreach ($this->products as $productsByStores) {
            foreach ($productsByStores as $storeId => $product) {
                // Create url rewrite for product.
                $designerName = $product->getData(\Ss\Designer\Model\Designer::ATTRIBUTE_CODE);

                $this->_urlRewriteHelper->setProduct($product);
                if ($this->_urlRewriteHelper->getTargetPath()) {
                    $this->_urlRewriteHelper->setPrefixUrlDesigner($this->_designers[$designerName]->getUrlPath());

                    $url = $this->_urlRewriteHelper->generateForSpecificStoreView($storeId);
                    $urls[] = array_shift($url);
                }
            }
        }

        return $urls;
    }

    /**
     * Check is global scope
     *
     * @param int|null $storeId
     * @return bool
     */
    protected function isGlobalScope($storeId)
    {
        return null === $storeId || $storeId == Store::DEFAULT_STORE_ID;
    }

}

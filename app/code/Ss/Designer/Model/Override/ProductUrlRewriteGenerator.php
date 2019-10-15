<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile

namespace Ss\Designer\Model\Override;

/**
 * ProductUrlRewriteGenerator model
 *
 */
class ProductUrlRewriteGenerator extends \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator
{

    protected $_designerFactory;

    /**
     * 
     * @param \Magento\CatalogUrlRewrite\Model\Product\CanonicalUrlRewriteGenerator $canonicalUrlRewriteGenerator
     * @param \Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewritesRegenerator $currentUrlRewritesRegenerator
     * @param \Magento\CatalogUrlRewrite\Model\Product\CategoriesUrlRewriteGenerator $categoriesUrlRewriteGenerator
     * @param \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory $objectRegistryFactory
     * @param \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ss\Designer\Model\DesignerFactory $designerFactory
     */
    public function __construct(\Magento\CatalogUrlRewrite\Model\Product\CanonicalUrlRewriteGenerator $canonicalUrlRewriteGenerator,
        \Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewritesRegenerator $currentUrlRewritesRegenerator,
        \Magento\CatalogUrlRewrite\Model\Product\CategoriesUrlRewriteGenerator $categoriesUrlRewriteGenerator,
        \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory $objectRegistryFactory,
        \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ss\Designer\Model\DesignerFactory $designerFactory
    )
    {
        parent::__construct($canonicalUrlRewriteGenerator, $currentUrlRewritesRegenerator, $categoriesUrlRewriteGenerator, $objectRegistryFactory, $storeViewService, $storeManager);

        $this->_designerFactory = $designerFactory;
    }

    /**
     * Generate list of urls for specific store view
     *
     * @param int $storeId
     * @param \Magento\Framework\Data\Collection $productCategories
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateForSpecificStoreView($storeId,
        $productCategories)
    {
        $designerId = $this->product->getSsDesigner();
        $designer = NULL;
        if (!empty($designerId)) {
            $designer = $this->_designerFactory->create()->load($designerId);
        }
        $categories = [];
        foreach ($productCategories as $category) {
            if ($this->isCategoryProperForGenerating($category, $storeId)) {
                $categories[] = $category;
            }
        }
        $this->productCategories = $this->objectRegistryFactory->create(['entities' => $categories]);
        /**
         * @var $urls \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
         */
        $urls = array_merge(
            $this->canonicalUrlRewriteGenerator->generate($storeId, $this->product),
            $this->categoriesUrlRewriteGenerator->generate($storeId, $this->product,$this->productCategories),
            $this->currentUrlRewritesRegenerator->generate($storeId, $this->product, $this->productCategories)
        );

        /* Reduce duplicates. Last wins */
        $result = [];
        foreach ($urls as $url) {
            $id = $url->getTargetPath();            
            if ($designer && strpos($url->getRequestPath(), \Ss\Designer\Model\Designer::PREFIX_URL_DESIGNER . '/') !== false) {
                $url->setTargetPath($designer->getUrlPath() . '/' . $id);
                $id = \Ss\Designer\Model\Designer::PREFIX_URL_DESIGNER . $id;
            }            
             
            $result[$id . '-' . $url->getStoreId()] = $url;
        }
        
        $this->productCategories = null;
        return $result;
    }

}

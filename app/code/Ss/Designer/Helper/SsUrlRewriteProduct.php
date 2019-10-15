<?php

/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */

namespace Ss\Designer\Helper;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Designer Url Rewrite
 */
class SsUrlRewriteProduct extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_urlPersistStorage;
    protected $_storeViewService;
    protected $_urlRewriteFactory;
    protected $_prefixUrlDesigner;
    protected $_targetPath;
    protected $_product;
    protected $_productUrlPathGenerator;
    protected $_themeHelper;
    protected $_isAttributeSetDiamond;
    protected $_isBackupUrl;
    protected $_oldUrlDesigner;
    protected $_isEditDesigner;
    protected $_logger;

    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Ss\Designer\Model\Override\Storage\DbStorage $urlPersist
     * @param \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory $urlRewriteFactory
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
        \Ss\Designer\Model\Override\Storage\DbStorage $urlPersist,
        \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService,
        \Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory $urlRewriteFactory,
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator,
        \Ss\Theme\Helper\Data $themeHelper
    )
    {
        parent::__construct($context);
        $this->_urlPersistStorage = $urlPersist;
        $this->_storeViewService = $storeViewService;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_productUrlPathGenerator = $productUrlPathGenerator;
        $this->_themeHelper = $themeHelper;
        $this->_isBackupUrl = FALSE;
        $this->_isEditDesigner = FALSE;
        $this->_logger = $context->getLogger();
    }

    /**
     * @todo to set target Path
     * @param type $targetPath
     */
    public function setTargetPath($targetPath)
    {
        if ($targetPath) {
            $this->_targetPath = $targetPath;
        }
    }

    /**
     * @todo to set prefix url designer
     * @param type $prefixUrlDesigner
     */
    public function setPrefixUrlDesigner($prefixUrlDesigner)
    {
        if ($prefixUrlDesigner) {
            $this->_prefixUrlDesigner = $prefixUrlDesigner;
        }
    }


    /**
     * @todo to set is backup url designer
     * @param type $isBackupUrl
     */
    public function setIsBackupUrl($isBackupUrl)
    {
        if ($isBackupUrl) {
            $this->_isBackupUrl = $isBackupUrl;
        }
    }

    /**
     * @todo to set is edit url designer
     * @param type $isEditDesigner
     */
    public function setIsEditDesigner($isEditDesigner)
    {
        if ($isEditDesigner) {
            $this->_isEditDesigner = $isEditDesigner;
        }
    }

    /**
     * @todo to set is backup url designer
     * @param type $oldUrlDesinger
     */
    public function setOldUrlDesigner($oldUrlDesinger)
    {
        if ($oldUrlDesinger) {
            $this->_oldUrlDesigner = $oldUrlDesinger;
        }
    }

    /**
     * @todo to set product
     * @param type $product
     */
    public function setProduct($product)
    {
        if ($product) {
            $this->_product = $product;
            $this->setTargetPath($this->_productUrlPathGenerator->getCanonicalUrlPath($product));

            $attributeSetDiamond = $this->_themeHelper->getConfigAttributeSetDiamond();
            $this->_isAttributeSetDiamond = ($product->getAttributeSetId() == $attributeSetDiamond) ? TRUE : FALSE;
        }
    }

    /**
     * @todo to get target path
     * @return type
     */
    public function getTargetPath()
    {
        return $this->_targetPath;
    }

    /**
     * @todo to get is backup url
     * @return type
     */
    public function getIsBackUpUrl()
    {
        return $this->_isBackupUrl;
    }

    /**
     * @todo to get is edit designer url
     * @return type
     */
    public function getIsEditDesigner()
    {
        return $this->_isEditDesigner;
    }

    /**
     * @todo to get old url designer
     * @return type
     */
    public function getOldUrlDesigner()
    {
        return $this->_oldUrlDesigner;
    }

    /**
     * Generate list of urls for global scope
     *
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generateForGlobalScope()
    {
        $urls = [];
        $productId = $this->_product->getId();
        foreach ($this->_product->getStoreIds() as $id) {
            if (!$this->isGlobalScope($id) && !$this->_storeViewService->doesEntityHaveOverriddenUrlKeyForStore($id, $productId, \Magento\Catalog\Model\Product::ENTITY)
            ) {
                $urls = array_merge($urls, $this->generateForSpecificStoreView($id));
            }
        }
        return $urls;
    }

    /**
     * Generate list of urls for specific store view
     *
     * @param int $storeId
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generateForSpecificStoreView($storeId)
    {
        $product = $this->_product;
        // Get url rule: url_designer/url_key_product.
        $requestPathUrl = $this->_prefixUrlDesigner . '/' . $product->getUrlKey();
        $result = [];

        // Add url rewrite for product designer.
        $data = [
            [
                'id' => \Ss\Designer\Model\Designer::ATTRIBUTE_CODE,
                'request_path' => $requestPathUrl
            ]
        ];

        // Add url rewrite for product diamond.
        if ($this->_isAttributeSetDiamond) {
            $data[] = [
                'id' => \Ss\Theme\Helper\Data::PREFIX_URL_REWRITE_DIAMONDS,
                'request_path' => \Ss\Theme\Helper\Data::PREFIX_URL_REWRITE_DIAMONDS . $product->getUrlKey()
            ];
        }

        foreach ($data as $item) {
            // Create url rewrite.
            $result[$item['id'] . '-' . $storeId] = $this->_urlRewriteFactory->create()
                ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                ->setEntityId($product->getId())
                ->setTargetPath($this->_targetPath)
                ->setStoreId($storeId)
                ->setRequestPath($item["request_path"])
            ;

            //Check if is backup then create url rewrite 301
            if($this->getIsBackUpUrl()){
                 $result[$item['id'] . '_' . $storeId] = $this->_urlRewriteFactory->create()
                ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                ->setEntityId($product->getId())
                ->setTargetPath($item["request_path"])
                ->setStoreId($storeId)
                ->setRequestPath($this->getOldUrlDesigner(). '/' . $product->getUrlKey())
                ->setRedirectType(OptionProvider::PERMANENT)
                ;
            }
        }



        return $result;
    }

    /**
     * Check is global scope
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isGlobalScope($storeId)
    {
        return null === $storeId || $storeId == \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }

    /**
     * @todo to insert url rewrite to db
     * @param type $urls
     */
    public function insertMultiple($urls)
    {

        if (!empty($urls)) {
            $data = [];
            foreach ($urls as $id => $url) {
                $idIsDiamond = explode('-', $id);
                $idIsDiamond = array_shift($idIsDiamond);
                $checkIsDiamond = ($idIsDiamond == \Ss\Designer\Model\Designer::ATTRIBUTE_CODE) ? FALSE : TRUE;

                $dataUrl = $url->toArray();
                $data[] = $dataUrl;
                // Delete old url before add new one.
                $this->deleteUrl($dataUrl, $checkIsDiamond);
            }
            $this->_urlPersistStorage->ssInsertMultiple($data);
        }


    }

    /**
     * @todo To delete url in db
     * @param type $dataUrl
     */
    public function deleteUrl($dataUrl,
        $isDiamondProduct = FALSE)
    {
        if (empty($dataUrl[UrlRewrite::ENTITY_ID]) ||  ! is_numeric($dataUrl[UrlRewrite::ENTITY_ID])) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Some url rewrite not found to delete.')
            );
        }

        $dataWhere = [
            UrlRewrite::ENTITY_ID => $dataUrl[UrlRewrite::ENTITY_ID],
            UrlRewrite::ENTITY_TYPE => $dataUrl[UrlRewrite::ENTITY_TYPE],
            UrlRewrite::REDIRECT_TYPE => [0],
        ];

        if($this->getIsEditDesigner()){
            $dataWhere[UrlRewrite::REDIRECT_TYPE][] = 301;
        }

        if (isset($dataUrl[UrlRewrite::STORE_ID])) {
            $dataWhere[UrlRewrite::STORE_ID] = $dataUrl[UrlRewrite::STORE_ID];
        }

        $select = $this->_urlPersistStorage->ssPrepareSelect($dataWhere);
        if (!$isDiamondProduct) {
            $select->where($this->_urlPersistStorage->getColumnName(UrlRewrite::REQUEST_PATH) . ' LIKE ?', \Ss\Designer\Model\Designer::PREFIX_URL_DESIGNER . '/%');
        } else {
            $select->where($this->_urlPersistStorage->getColumnName(UrlRewrite::REQUEST_PATH) . ' LIKE ?', \Ss\Theme\Helper\Data::PREFIX_URL_REWRITE_DIAMONDS . '%');
        }

        try {
            $this->_urlPersistStorage->ssDeleteFromSelect($select);
        } catch (\Exception $e) {
            $this->_logger->error($select->__toString());
        }
    }

    /**
     * @todo to generate url rewrite
     */
    public function generateUrlRewrite($insert = TRUE)
    {
        $storeId = $this->_product->getStoreId();
        // Generate url follow store id.
        if ($this->isGlobalScope($storeId)) {
            $urls = $this->generateForGlobalScope();
        } else {
            $urls = $this->generateForSpecificStoreView($storeId);
        }

        if ($insert) {
            // Insert url rewrite to table.
            $this->insertMultiple($urls);
        }
    }

    /**
     * @todo Remove url product
     * @param type $product
     */
    public function deleteUrlProduct($product)
    {
        $this->_product = $product;

        $data = [
            UrlRewrite::ENTITY_ID => $product->getId(),
            UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
        ];

        $this->deleteUrl($data);
    }

}

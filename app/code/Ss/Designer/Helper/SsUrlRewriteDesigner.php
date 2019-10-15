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

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\OptionProvider;

/**
 * Designer Url Rewrite
 */
class SsUrlRewriteDesigner extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_urlPersistStorage;
    protected $_urlRewriteFactory;
    protected $_requestPath;
    protected $_targetPath;
    protected $_defaultStore;
    protected $_defaultUrlType;
    protected $_isBackupUrl;
    protected $_oldUrlDesigner;
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
    \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory
    )
    {
        parent::__construct($context);
        $this->_urlPersistStorage = $urlPersist;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_defaultStore = \Magento\Store\Model\Store::DISTRO_STORE_ID;
        $this->_defaultUrlType = \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite::ENTITY_TYPE_CUSTOM;
        $this->_logger = $context->getLogger();
        $this->_isBackupUrl = FALSE;
    }

    /**
     * @todo to set target path
     * @param type $targetPath
     */
    public function setTargetPath($targetPath)
    {
        if ($targetPath) {
            $this->_targetPath = $targetPath;
        }
    }

    /**
     * @todo to set request path
     * @param type $requestPath
     */
    public function setRequestPath($requestPath)
    {
        if ($requestPath) {
            $this->_requestPath = $requestPath;
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
     * @todo to get is backup url
     * @return type
     */
    public function getIsBackUpUrl()
    {
        return $this->_isBackupUrl;
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
     * Generate list of urls for specific store view
     *
     * @param int $storeId
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function createUrlRewrite()
    {
        // Create url rewrite.
        $result = $this->_urlRewriteFactory->create()
            ->setEntityType($this->_defaultUrlType)
            ->setStoreId($this->_defaultStore)
            ->setTargetPath($this->_targetPath)
            ->setRequestPath($this->_requestPath)
            ->save()
        ;

        return $result;
    }

    /**
     * @todo to update url rewrite
     */
    public function updateUrlRewrite()
    {
        // get id url rewrite from old url_key
        $idUrlRewrite = $this->findIdUrlRewrite();
        $this->deleteOldUrlRewritePermanent();
        if ($idUrlRewrite) {
            $this->_urlRewriteFactory->create()->load($idUrlRewrite)
                ->setTargetPath($this->_targetPath)
                ->setRequestPath($this->_requestPath)
                ->save();

            if($this->getIsBackUpUrl()){
                $this->_urlRewriteFactory->create()
                    ->setEntityType($this->_defaultUrlType)
                    ->setStoreId($this->_defaultStore)
                    ->setTargetPath($this->_requestPath)
                    ->setRequestPath($this->getOldUrlDesigner())
                    ->setRedirectType(OptionProvider::PERMANENT)
                    ->save()
                ;
            }
        } else {
            $this->createUrlRewrite();
        }
    }

    /**
     * @todo To delete url rewrite
     */
    public function deleteUrlRewrite()
    {
        $dataWhere = [
            UrlRewrite::ENTITY_TYPE => $this->_defaultUrlType,
            UrlRewrite::STORE_ID => $this->_defaultStore,
            UrlRewrite::TARGET_PATH => $this->_targetPath
        ];

        $select = $this->_urlPersistStorage->ssPrepareSelect($dataWhere);

        try {
            $this->_urlPersistStorage->ssDeleteFromSelect($select);
        } catch (\Exception $e) {
            $this->_logger->error($select->__toString());
        }
    }

    /**
     * @todo To delete url in db
     * @return boolean
     */
    public function findIdUrlRewrite()
    {
        $dataWhere = [
            UrlRewrite::ENTITY_TYPE => $this->_defaultUrlType,
            UrlRewrite::STORE_ID => $this->_defaultStore,
            UrlRewrite::TARGET_PATH => $this->_targetPath
        ];

        $fetchOne = $this->_urlPersistStorage->ssFindIdUrlRewrite($dataWhere);
        if ($fetchOne) {
            return array_shift($fetchOne);
        }

        return false;
    }

    /**
     * @todo To delete old url 301 in db
     * @return boolean
     */
    public function deleteOldUrlRewritePermanent()
    {
        $dataWhere = [
            UrlRewrite::ENTITY_TYPE => $this->_defaultUrlType,
            UrlRewrite::STORE_ID => $this->_defaultStore,
            UrlRewrite::TARGET_PATH => $this->getOldUrlDesigner(),
            UrlRewrite::REDIRECT_TYPE => 301,
        ];

        $select = $this->_urlPersistStorage->ssPrepareSelect($dataWhere);

        try {
            $this->_urlPersistStorage->ssDeleteFromSelect($select);
        } catch (\Exception $e) {
            $this->_logger->error($select->__toString());
        }
    }

}

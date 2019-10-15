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

namespace Ss\Collection\Helper;

/**
 * Collection helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;
    protected $_collectionFactory;
    protected $_collectionCollectionFactory;
    protected $_productCollectionFactory;
    protected $_catalogProductVisibility;
    protected $_categoryCollectionFactory;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Backend\Model\UrlInterface $backendUrl, \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory, \Ss\Collection\Model\CollectionFactory $collectionFactory, \Ss\Collection\Model\ResourceModel\Collection\CollectionFactory $collectionCollectionFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
    )
    {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_collectionCollectionFactory = $collectionCollectionFactory;
    }

    /**
     * @todo To get image transparent
     * @return type
     */
    public function getSrcImageTransparent()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC) . 'frontend/Ss/stonestrand/en_US/images/transparent.png';
    }

    /**
     * @todo to get media url
     * @return type
     */
    public function getSrcMediaImage()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @todo to get collection by id
     * @param type $collectionId
     * @return boolean
     */
    public function getCollectionById($collectionId)
    {
        if (!$collectionId || empty($collectionId)) {
            return false;
        }
        return $this->_collectionFactory->create()->load($collectionId);
    }
}

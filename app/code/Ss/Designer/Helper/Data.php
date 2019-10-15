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

/**
 * Designer helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;
    protected $_designerFactory;
    protected $_designerCollectionFactory;
    protected $_productCollectionFactory;
    protected $_catalogProductVisibility;
    protected $_categoryCollectionFactory;
    protected $_urlBuider;
    protected $_request;
    protected $_catalogConfig;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Ss\Designer\Model\DesignerFactory $designerFactory,
        \Ss\Designer\Model\ResourceModel\Designer\CollectionFactory $designerCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Action\Context $contextRequest,
        \Magento\Catalog\Model\Config $catalogConfig
    )
    {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->_storeManager = $storeManager;
        $this->_designerFactory = $designerFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_designerCollectionFactory = $designerCollectionFactory;
        $this->_urlBuider = $context->getUrlBuilder();
        $this->_request = $contextRequest->getRequest();
        $this->_catalogConfig = $catalogConfig;
    }

    /**
     * get Slider Banner Url
     * @return string
     */
    public function getTagsDesignerUrl()
    {
        return $this->_backendUrl->getUrl('*/*/tags', ['_current' => true]);
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
     * @todo To get default image url category
     * @return type
     */
    public function getDefaultImageCategory(){
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC) . 'frontend/Ss/stonestrand/en_US/images/upload/img-1000x300.png';
    }

    /**
     * @todo to get media url
     * @return type
     */
    public function getSrcMediaImage()
    {
        return $this->_urlBuider
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA, '_secure' => $this->_request->isSecure()]);
    }

    /**
     * @todo to get designer by id
     * @param type $designerId
     * @return boolean
     */
    public function getDesignerById($designerId)
    {
        if (!$designerId || empty($designerId)) {
            return FALSE;
        }

        $designer = $this->_designerFactory->create()->load($designerId);
        return ($designer->getIsActive()) ? $designer : FALSE;
    }

    /**
     * @todo to get designer listing page
     * @return type
     */
    public function getDesignerListingUrl()
    {
        return $this->_getUrl('/') . \Ss\Theme\Helper\Data::URL_DESIGNER_LISTING_PAGE;
    }

    /**
     * @todo to get jewelry page
     * @return type
     */
    public function getJewelryUrlPage()
    {
        return $this->_getUrl('/') . \Ss\Theme\Helper\Data::URL_JEWELRY_PAGE;
    }
    
    /**
     * @todo to get jewelry page
     * @return type
     */
    public function getJewelryVintageUrlPage()
    {
        return $this->_getUrl('/') . \Ss\Theme\Helper\Data::URL_JEWELRY_VINTAGE_PAGE;
    }

    /**
     * @todo to get diamonds page
     * @return type
     */
    public function getDiamondUrlPage()
    {
        return $this->_getUrl('/') . \Ss\Theme\Helper\Data::URL_DIAMONDS_PAGE;
    }

    /**
     * @todo to get capsule page.
     * @return type
     */
    public function getCapsuleUrlPage()
    {
        return $this->_getUrl('/') . \Ss\Theme\Helper\Data::URL_POPUP_DESIGNER_PAGE;
    }
    
    /**
     * @todo to get capsule page.
     * @return type
     */
    public function getGiveBackUrlPage()
    {
        return $this->_getUrl('/') . \Ss\Theme\Helper\Data::URL_GIVEBACK_DESIGNER_PAGE;
    }

    /**
     * @todo to get contact us page.
     * @return type
     */
    public function getContactUsUrlPage()
    {
        return $this->_getUrl('/') . \Ss\Theme\Helper\Data::URL_CONTACT_US_PAGE;
    }

    /**
     * @todo to get list product by designer id.
     * @param type $designerId
     * @param type $excludeProductIds
     * @param type $limit
     * @return boolean
     */
    public function getProductsByDesignerId($designerId,
        $excludeProductIds = '',
        $limit = '')
    {        
        if (!$designerId || empty($designerId)) {
            return [];
        }

        // Check exist designer.
        $designer = $this->_designerFactory->create()->load($designerId);
        if (!$designer->getDesignerId() || !$designer->getIsActive()) {
            return [];
        }
        
        $attributes = $this->_catalogConfig->getProductAttributes();
        
        $collection = $this->_productCollectionFactory->create()
            ->addAttributeToFilter(\Ss\Designer\Model\Designer::ATTRIBUTE_CODE, ['IN' => $designerId])
            ->addAttributeToFilter(\Ss\Theme\Helper\Attributes::PRODUCT_TYPE, ['neq' => \Ss\Theme\Helper\Attributes::PRODUCT_TYPE_VIRTUAL])
            ->addAttributeToSelect($attributes)
            ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        if (!empty($excludeProductIds)) {
            $collection->addAttributeToFilter('entity_id', ['nin' => $excludeProductIds]);
        }

        if (!empty($limit)) {
            $collection->setPageSize($limit);
        }

        return $collection->load();
    }

    /**
     * @todo To group designer by type
     * @param type $listDesignerIds
     * @param type $listDesignerLink
     * @return boolean
     */
    public function groupDesignerByType($listDesignerIds,
        $listDesignerLink)
    {
        if (empty($listDesignerIds)) {
            return FALSE;
        }

        $results = [];
        $collection = $this->_designerCollectionFactory->create()->groupDesignerByType($listDesignerIds);
        foreach ($collection as $item) {
            $results[$item->getTypeId()]['title'] = $item->getTypeName() . __(' Designer');
            $results[$item->getTypeId()]['items'][] = [
                'id' => $item->getDesignerId(),
                'name' => $item->getName(),
                'count' => 2,
                'url' => $listDesignerLink[$item->getDesignerId()]
            ];
        }

        return $results;
    }

    /**
     * @todo Get filter  url for sub category on designer page
     * @param type $attributeCode
     * @param type $categoryId
     * @return type
     */
    public function getFilterCategoryUrl($attributeCode,
        $categoryId)
    {
        $query = [$attributeCode => $categoryId];
        $query['isAjax'] = null;
        $query['_'] = null;

        return $this->_getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    public function getDesignerIdByOptionId($optionId)
    {
        return $this->_designerFactory->create()->getResource()->getDesignerIdByOptionId($optionId);
    }

}

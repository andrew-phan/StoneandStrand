<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magefan\Blog\Block\Widget;

/**
 * Banner Widget Block
 *
 */
class Post extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;
    protected $_storeManager;
    protected $_themeHelper;
    protected $_designerHelper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Ss\Theme\Helper\Data $helper, \Ss\Designer\Helper\Data $designerHelper, \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory, array $data = [])
    {
        $this->_themeHelper = $helper;
        $this->_designerHelper = $designerHelper;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate($this->getTemplateName());
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Get Theme helper
     * @return type
     */
    public function getThemeHelper()
    {
        return $this->_themeHelper;
    }

    /**
     * Get Title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
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
     * Get YouTube URL
     * @return string
     */
    public function getYoutubeId()
    {
        return $this->getData('youtube_id');
    }

    /**
     * Get templateName
     */
    public function getTemplateName()
    {
        return !empty($this->getData('template')) ? $this->getData('template') : 'widget/post.phtml';
    }

    /**
     * Get post Ids
     * @return string
     */
    public function getPostIds()
    {
        return $this->getData('post_ids');
    }

    /**
     * Get Post by Id
     * @param int $postId
     * @return object|boolean
     */
    public function getPostById($postId)
    {
        if (!isset($postId) || empty($postId)) {
            return false;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('Magefan\Blog\Model\Post')->load($postId);
    }

    /**
     * Get Post
     * @return object|boolean
     */
    public function getPost()
    {
        $post_ids = $this->getPostIds();
        if (!$post_ids || empty($post_ids)) {
            return false;
        }

        $templateName = $this->getTemplateName();
        switch ($templateName) {
            case '':
                break;
        }
    }

    /**
     * Get list category id
     * @return string
     */
    public function getListCategoryIds()
    {
        return $this->getData('category_ids');
    }

    /**
     * Get Jewelry Category
     * @return string
     */
    public function getJewelryCateogry()
    {
        return $this->getData('jewelry_category_id');
    }

    /**
     * Get Category Collection
     * @return collection
     */
    public function getCategoryCollection($type = 'designer')
    {
        $storeId = $this->getStore()->getId();
        switch ($type) {
            case 'jewelry':
                $catIds = explode(',', $this->getJewelryCateogry());
                break;
            case 'designer':
            default:
                $catIds = explode(',', $this->getListCategoryIds());
                break;
        }

        $collection = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $catIds])
            ->addFieldToFilter('is_active', 1)
            ->setOrder('position', 'ASC');
        $collection->setStoreId($storeId);
        return $collection;
    }

    /**
     * get transparent image url
     * @return string
     */
    public function getSrcImageTransparent()
    {
        return $this->_designerHelper->getSrcImageTransparent();
    }

    /**
     * Get source image url
     * @return string
     */
    public function getSrcMediaImage()
    {
        return $this->_designerHelper->getSrcMediaImage();
    }

    /**
     * Get visit us url
     * @return string
     */
    public function getVisitUsUrl()
    {
        return $this->getData("visit_us_url");
    }

    /**
     * Get visit us url
     * @return string
     */
    public function getShopAllDesignerUrl()
    {
        return $this->getData("shop_all_designer_url");
    }

    /**
     * Get terms & conditions url
     * @return string
     */
    public function getTermsConditionUrl()
    {
        return $this->getData("term_condition_url");
    }

    /**
     * Get policy url
     * @return string
     */
    public function getUrlPolicy()
    {
        return $this->getData("policy_url");
    }

    /**
     * Get care and repair url
     * @return string
     */
    public function getUrlCare()
    {
        return $this->getData("care_url");
    }

    /**
     * Get return and exchange url
     * @return string
     */
    public function getUrlExchange()
    {
        return $this->getData("exchange_url");
    }

    /**
     * Get shipping url
     * @return string
     */
    public function getUrlShipping()
    {
        return $this->getData("shipping_url");
    }

    /**
     * Get lifetime warranty url
     * @return string
     */
    public function getUrlWarranty()
    {
        return $this->getData("warranty_url");
    }

    /**
     * Get faq url
     * @return string
     */
    public function getUrlFaq()
    {
        return $this->getData("faq_url");
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

    /**
     * @todo to check is Collapse diamond filter
     * @return boolean
     */
    public function isCollapseDiamondFilter()
    {
        $paramCollapse = $this->_request->getParam(\Ss\Theme\Helper\Data::PARAM_COLLAPSE, '');
        if (!empty($paramCollapse) && ($paramCollapse == "true" || $paramCollapse == "1")) {
            return TRUE;
        }

        return FALSE;
    }

}

<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Block\Widget;

/**
 * Category Widget Block
 *
 */
class Category extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;
    protected $_storeManager;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = array()
    )
    {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/category_menu.phtml');
        $this->addData([
            'cache_lifetime' => 86400,
        ]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {

        $template = $this->getTemplate();
        $template = explode('/', $template);
        $name = array_pop($template);
        return [
            'BLOCK_TPL_' . $name,
            $this->_storeManager->getStore()->getCode(),
            $this->getTemplateFile(),
            'template' => $this->getTemplate()
        ];

    }
    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * @todo To get title widget
     * @return type
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * * @todo To get column number widget
     * @return type
     */
    public function getColumnNumber()
    {
        return $this->getData('column_number');
    }

    /**
     * @todo To get list category ids in widget
     * @return type
     */
    public function getListCategoryIds()
    {
        return $this->getData('category_ids');
    }

    /**
     * @todo to create all category collections
     * @return type
     */
    public function createCategoryCollections()
    {
        $storeId = $this->getStore()->getId();
        $categoryIds = explode(',', $this->getListCategoryIds());
        $collection = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('id')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('parent_id')
            ->addAttributeToSelect('url')
            ->addFieldToFilter('entity_id', ['in' => $categoryIds])
            ->addFieldToFilter('is_active', 1)
            ->setOrder('position', 'ASC');
        $collection->setStoreId($storeId);
        return $collection;
    }

    /**
     * @todo To get all category from category collection
     * @return type
     */
    public function getAllCategories()
    {
        $collection = $this->createCategoryCollections();
        $categories = array();
        
        foreach ($collection as $category) {
            if ($category->hasChildren()) {
                $category->setLevel(1);
                $key = $category->getId();
            } else {
                $category->setLevel(2);
                $key = $category->getParentId();
            }
            
            if (!isset($categories[$key])) {
                $categories[$key] = [];
            }
            
            if ($key == $category->getId()) {
                array_unshift($categories[$key], $category);
            } else {
                array_push($categories[$key], $category);
            }
        }
        
        ksort($categories);

        return $categories;
    }

    /**
     * @todo to get level class.
     * @param type $level
     * @return string
     */
    public function getLevel($level)
    {
        switch ($level) {
            case 1:
                $class = 'level1';
                break;
            case 2:
                $class = 'level2';
                break;
            case 3:
                $class = 'level3';
                break;
            default:
                $class = '';
        }
        return $class;
    }

}

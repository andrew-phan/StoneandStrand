<?php

/**
 * Magestore
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
 * @category    Magestore
 * @package     Magestore_Bannerslider
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Ss\Designer\Block\Adminhtml\Designer\Edit\Tab;

/**
 * Banners tab.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Tags extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * banner factory.
     *
     * @var \Magestore\Bannerslider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_tagsCollectionFactory;
    protected $_designerFactory;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Magestore\Bannerslider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory
     * @param array                                                           $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ss\Designer\Model\ResourceModel\Tags\CollectionFactory $tagsCollectionFactory,
        \Ss\Designer\Model\DesignerFactory $designerFactory,
        array $data = []
    )
    {
        $this->_tagsCollectionFactory = $tagsCollectionFactory;
        $this->_designerFactory = $designerFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('tagsGrid');
        $this->setDefaultSort('tag_id1');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('designer_id')) {
            $this->setDefaultFilter(array(
                'in_tag' => 1));
        }
    }

    /**
     * add Column Filter To Collection
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_tag') {
            $tagIds = $this->getSelectedDesignerTags();
            if (empty($tagIds)) {
                $tagIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('tag_id', array(
                    'in' => $tagIds));
            } else {
                if ($tagIds) {
                    $this->getCollection()->addFieldToFilter('tag_id', array(
                        'nin' => $tagIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
        /** @var \Magestore\Bannerslider\Model\ResourceModel\Banner\Collection $collection */
        $collection = $this->_tagsCollectionFactory->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_tag', [
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_tag',
            'align' => 'center',
            'index' => 'tag_id',
            'values' => $this->getSelectedDesignerTags(),
            ]
        );

        $this->addColumn(
            'tag_id1', [
            'header' => __('Tag ID'),
            'index' => 'tag_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name1', [
            'header' => __('Name'),
            'index' => 'name',
            'class' => 'xxx',
            'width' => '50px',
            ]
        );

        $this->addColumn(
            'description1', [
            'header' => __('Description'),
            'index' => 'description',
            'class' => 'xxx',
            ]
        );

        $this->addColumn(
            'edit', [
            'header' => __('Edit'),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'header_css_class' => 'col-action',
            'column_css_class' => 'col-action',
            'renderer' => 'Ss\Designer\Block\Adminhtml\Designer\Edit\Tab\Helper\Renderer\EditTag',
            ]
        );

        $this->addColumn(
            'order_designer_tags', [
            'header' => __('Order'),
            'name' => 'order_designer_tags',
            'index' => 'order_designer_tags',
            'class' => 'xxx',
            'width' => '50px',
            'editable' => true,
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/tagsgrid', ['_current' => true]);
    }

    /**
     * get row url
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    public function getSelectedDesignerTags()
    {
        $designerId = $this->getRequest()->getParam('designer_id');
        if (!isset($designerId)) {
            return [];
        }

        // Get list Tag follow designer_id.
        $designerResource = $this->_designerFactory->create()->getResource();
        $tagIds = $designerResource->getTagIds($designerId);

        return $tagIds;
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Tags');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Tags');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }

}

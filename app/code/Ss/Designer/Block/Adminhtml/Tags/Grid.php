<?php

/**
 *
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

namespace Ss\Designer\Block\Adminhtml\Tags;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_tagCollection;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ss\Designer\Model\ResourceModel\Tags\Collection $tagCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ss\Designer\Model\ResourceModel\Tags\Collection $tagCollection,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_tagCollection = $tagCollection;
        $this->_wysiwygConfig = $wysiwygConfig;

        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('tagGrid');
        $this->setDefaultSort('tag_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        /** @var \Magestore\Bannerslider\Model\ResourceModel\Banner\Collection $collection */
        $this->setCollection($this->_tagCollection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'tag_id', [
            'header' => __('Tag ID'),
            'index' => 'tag_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'width' => '70px',
            ]
        );

        $this->addColumn(
            'name', [
            'header' => __('Name'),
            'index' => 'name',
            'class' => 'xxx',
            'width' => '50px',
            ]
        );

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        $this->addColumn(
            'description', [
            'header' => __('Description'),
            'index' => 'description',
            'class' => 'xxx',
            'config' => $wysiwygConfig
            ]
        );


        $this->addColumn(
            'edit', [
            'header' => __('Edit'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
                [
                    'caption' => __('Edit'),
                    'url' => ['base' => '*/*/edit'],
                    'field' => 'tag_id',
                ],
            ],
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'header_css_class' => 'col-action',
            'column_css_class' => 'col-action',
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('tags');

        $this->getMassactionBlock()->addItem(
            'delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('stone_designer/*/massDelete'),
            'confirm' => __('Are you sure?'),
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * get row url
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
                '*/*/edit', ['tag_id' => $row->getId()]
        );
    }

}

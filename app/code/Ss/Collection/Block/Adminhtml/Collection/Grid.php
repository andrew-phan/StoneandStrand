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

 */

namespace Ss\Collection\Block\Adminhtml\Collection;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * banner collection factory.
     *
     * @var \Magestore\Bannerslider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_collectionCollection;
    protected $_typeCollection;
    protected $_tagCollection;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ss\Collection\Model\ResourceModel\Collection\Collection $collectionCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Ss\Collection\Model\ResourceModel\Collection\Collection $collectionCollection, array $data = []
    )
    {

        $this->_collectionCollection = $collectionCollection;

        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('collectionGrid');
        $this->setDefaultSort('main_table.collection_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $this->setCollection($this->_collectionCollection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'collection_id', [
            'header' => __('Collection ID'),
            'index' => 'collection_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'width' => '70px',
            ]
        );

        $this->addColumn(
            'image', [
            'header' => __('Image'),
            'class' => 'xxx',
            'width' => '50px',
            'filter' => false,
            'renderer' => 'Ss\Collection\Block\Adminhtml\Collection\Helper\Renderer\Image',
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

        $this->addColumn(
            'url_key', [
            'header' => __('Url key'),
            'index' => 'url_key',
            'class' => 'xxx',
            'width' => '50px',
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
                    'field' => 'collection_id',
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
        $this->getMassactionBlock()->setFormFieldName('collection');

        $this->getMassactionBlock()->addItem(
            'delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('ss_collection/*/massDelete'),
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
                '*/*/edit', ['collection_id' => $row->getId()]
        );
    }

}

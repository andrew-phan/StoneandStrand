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

namespace Ss\Slideshow\Block\Adminhtml\Banner;

use Ss\Slideshow\Model\Banner;

/**
 * Banner grid.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * banner collection factory.
     *
     * @var \Magestore\Bannerslider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_bannerCollectionFactory;

    /**
     * construct.
     *
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Magestore\Bannerslider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory
     * @param array                                                           $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Ss\Slideshow\Model\ResourceModel\Banner\Collection $bannerCollectionFactory, array $data = []
    )
    {
        $this->_bannerCollectionFactory = $bannerCollectionFactory;

        parent::__construct($context, $backendHelper, $data);

    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('bannerGrid');
        $this->setDefaultSort('banner_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }

    protected function _prepareCollection()
    {

        /** @var \Magestore\Bannerslider\Model\ResourceModel\Banner\Collection $collection */
        $this->setCollection($this->_bannerCollectionFactory);

        return parent::_prepareCollection();

    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'banner_id', [
            'header' => __('Banner ID'),
            'index' => 'banner_id',
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
            'renderer' => 'Ss\Slideshow\Block\Adminhtml\Banner\Helper\Renderer\Image',
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
            'target_url', [
            'header' => __('Target Url'),
            'index' => 'target_url',
            'class' => 'xxx',
            'width' => '50px',
            ]
        );

        $this->addColumn(
            'description', [
            'header' => __('Description'),
            'index' => 'description',
            'class' => 'xxx',
            ]
        );

        $this->addColumn(
            'order_banner', [
            'header' => __('Order'),
            'index' => 'order_banner',
            'class' => 'xxx',
            ]
        );

        $this->addColumn(
            'banner_type', [
            'header' => __('Type'),
            'index' => 'banner_type',
            'class' => 'xxx',
            ]
        );
        $this->addColumn(
            'banner_type', [
            'header' => __('Type'),
            'index' => 'banner_type',
            'type' => 'options',
            'options' => Banner::getBannerType(),
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
                    'url' => ['base' => 'ss_slideshow/banner/edit'],
                    'field' => 'banner_id',
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
        $this->getMassactionBlock()->setFormFieldName('banner');

        $this->getMassactionBlock()->addItem(
            'delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('ss_slideshow/*/massDelete'),
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
                '*/*/edit', ['banner_id' => $row->getId()]
        );

    }
}

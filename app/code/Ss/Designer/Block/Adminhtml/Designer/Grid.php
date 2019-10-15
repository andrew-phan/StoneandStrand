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

namespace Ss\Designer\Block\Adminhtml\Designer;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * banner collection factory.
     *
     * @var \Magestore\Bannerslider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_designerCollection;
    protected $_typeCollection;
    protected $_tagCollection;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ss\Designer\Model\ResourceModel\Designer\Collection $designerCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ss\Designer\Model\ResourceModel\Designer\Collection $designerCollection,
        \Ss\Designer\Model\ResourceModel\Tags\Collection $tagCollection,
        \Ss\Designer\Model\ResourceModel\Type\Collection $typeCollection,
        array $data = []
    )
    {

        $this->_designerCollection = $designerCollection;
        $this->_tagCollection = $tagCollection;
        $this->_typeCollection = $typeCollection;

        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('designerGrid');
        $this->setDefaultSort('main_table.designer_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $this->setCollection($this->_designerCollection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'designer_id', [
            'header' => __('Designer ID'),
            'index' => 'designer_id',
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
            'renderer' => 'Ss\Designer\Block\Adminhtml\Designer\Helper\Renderer\Image',
            ]
        );

        $this->addColumn(
            'image_product', [
            'header' => __('Image product'),
            'class' => 'xxx',
            'width' => '50px',
            'filter' => false,
            'renderer' => 'Ss\Designer\Block\Adminhtml\Designer\Helper\Renderer\ProductImage',
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
            'type_id', [
            'header' => __('Type'),
            'index' => 'type_id',
            'type' => 'options',
            'options' => $this->_typeCollection->getOptionsTypes(FALSE),
            'class' => 'xxx',
            'width' => '50px',
            'renderer' => 'Ss\Designer\Block\Adminhtml\Designer\Helper\Renderer\Type',
            ]
        );

        $this->addColumn(
            'filter_grid_tag_ids', [
            'header' => __('Tags'),
            'index' => 'filter_grid_tag_ids',
            'type' => 'options',
            'options' => $this->_tagCollection->getOptionsTags(FALSE),
            'class' => 'xxx',
            'width' => '50px',
            'renderer' => 'Ss\Designer\Block\Adminhtml\Designer\Helper\Renderer\Tags',
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
                    'url' => ['base' => 'stone_designer/designer/edit'],
                    'field' => 'designer_id',
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
        $this->getMassactionBlock()->setFormFieldName('designer');

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
                '*/*/edit', ['designer_id' => $row->getId()]
        );
    }

}

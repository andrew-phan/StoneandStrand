<?php

namespace Ss\Designer\Block\Adminhtml\Type;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * banner collection factory.
     *
     * @var \Magestore\Bannerslider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_typeCollection;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ss\Designer\Model\ResourceModel\Type\Collection $typeCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ss\Designer\Model\ResourceModel\Type\Collection $typeCollection,
        array $data = []
    )
    {
        $this->_typeCollection = $typeCollection;

        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('typeGrid');
        $this->setDefaultSort('type_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {

        /** @var \Magestore\Bannerslider\Model\ResourceModel\Banner\Collection $collection */
        $this->setCollection($this->_typeCollection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'type_id', [
            'header' => __('Type ID'),
            'index' => 'type_id',
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

        $this->addColumn(
            'is_vintage', [
            'header' => __('Vintage template'),
            'index' => 'is_vintage',
            'type' => 'options',
            'options' => \Ss\Designer\Model\Type::STATUS_OPTION,
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
            'edit', [
            'header' => __('Edit'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
                [
                    'caption' => __('Edit'),
                    'url' => ['base' => '*/*/edit'],
                    'field' => 'type_id',
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
        $this->getMassactionBlock()->setFormFieldName('type');

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
                '*/*/edit', ['type_id' => $row->getId()]
        );
    }

}

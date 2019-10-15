<?php

namespace Ss\Designer\Model;

class Tags extends \Magento\Framework\Model\AbstractModel
{

    protected $_designerHelper;

    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Ss\Designer\Helper\Data $designerHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ss\Designer\Helper\Data $designerHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb
    $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_designerHelper = $designerHelper;
    }

    public function _construct()
    {
        $this->_init('Ss\Designer\Model\ResourceModel\Tags');
    }

    /**
     * @todo to get url for tag
     * @return type
     */
    public function getUrl()
    {
        return $this->_designerHelper->getDesignerListingUrl() . '?tag_id=' . $this->getTagId();
    }

}

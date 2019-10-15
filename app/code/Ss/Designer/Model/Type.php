<?php

namespace Ss\Designer\Model;

/**
 * Class Type
 */
class Type extends \Magento\Framework\Model\AbstractModel
{

    const STATUS_OPTION = [0 => 'No', 1 => 'Yes'];

    protected $_urlBuilder;

    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_urlBuilder = $urlBuilder;
    }

    public function _construct()
    {
        $this->_init('Ss\Designer\Model\ResourceModel\Type');
    }

    /**
     * @todo to get list type option
     * @return type
     */
    public function getListOptions()
    {
        $collection = $this->getCollection();
        $options = ['' => __('Select Type')];
        foreach ($collection as $type) {
            $options[$type->getTypeId()] = $type->getName();
        }

        return $options;
    }

    /**
     * To get detail designer page
     * @return type
     */
    public function getUrl()
    {
        return '#';
    }

}

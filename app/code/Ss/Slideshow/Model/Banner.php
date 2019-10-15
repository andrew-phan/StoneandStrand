<?php

namespace Ss\Slideshow\Model;

/**
 * Banner model
 */
class Banner extends \Magento\Framework\Model\AbstractModel
{

    const BASE_MEDIA_PATH = 'slideshow';

    static $_bannerType = [
        'homepage' => 'Homepage',
        'diamond' => 'Diamond'
    ];

    /**
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb
    $resourceCollection = null, array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

    }

    public function _construct()
    {
        $this->_init('Ss\Slideshow\Model\ResourceModel\Banner');

    }

    /**
     * Get Banner Type
     * @return array
     */
    public static function getBannerType()
    {
        return static::$_bannerType;

    }
}

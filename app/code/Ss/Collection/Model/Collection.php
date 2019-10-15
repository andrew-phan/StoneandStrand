<?php

namespace Ss\Collection\Model;

use Magento\Framework\DataObject\IdentityInterface;

class Collection extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'ss_collection';
    const ATTRIBUTE_CODE = 'ss_collection';
    const BASE_MEDIA_PATH = 'collection';
    const LIMIT_ITEM_MENU = 2;

    protected $_prouctCollectionFactory = null;
    protected $_urlBuilder;
    protected $_themeHelper;
    protected $_request;

    public function __construct(
    \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Helper\Context $contextHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $prouctCollectionFactory,
        \Ss\Theme\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $contextRequest,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_urlBuilder = $contextHelper->getUrlBuilder();
        $this->_prouctCollectionFactory = $prouctCollectionFactory;
        $this->_themeHelper = $helper;
        $this->_request = $contextRequest->getRequest();

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init('Ss\Collection\Model\ResourceModel\Collection');
    }

    /**
     * To get detail collection page
     * @return type
     */
    public function getUrl()
    {
        return $this->_urlBuilder->getUrl('collection/' . $this->getUrlKey());
    }

    /**
     * @todo To get menu url collection
     * @return string
     */
    public function getMenuUrl()
    {
        if ($this->getOptionId()) {
            return $this->_urlBuilder->getUrl(\Ss\Theme\Helper\Data::URL_JEWELRY_PAGE . '?' . static::ATTRIBUTE_CODE . '=' . $this->getOptionId());            
        }

        return '';
    }

    /**
     * Check if post url key exists
     * return post id if post exists
     *
     * @param string $url_key
     * @return int
     */
    public function checkExistUrlKey($url_key)
    {
        return $this->_getResource()->checkExistUrlKey($url_key);
    }

    /**
     * @todo To get feature image
     * @return type
     */
    public function getFeatureImageUrl()
    {
        $featureImage = $this->getImage();
        if (empty($featureImage)) {
            return $this->_themeHelper->getPlaceHolderImage();
        }

        return $this->_urlBuilder
            ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA, '_secure' => $this->_request->isSecure()]) . $featureImage;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [static::CACHE_TAG . '_' . $this->getId()];
    }

}

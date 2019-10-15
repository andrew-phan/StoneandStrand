<?php

namespace Ss\Collection\Block\Widget;

class Collections extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Ashsmith\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    protected $_collectionCollectionFactory;
    protected $_designerHelper;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Collection\Model\ResourceModel\Collection\CollectionFactory $collectionCollectionFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Ss\Collection\Model\ResourceModel\Collection\CollectionFactory $collectionCollectionFactory,\Ss\Designer\Helper\Data $designerHelper, array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_collectionCollectionFactory = $collectionCollectionFactory;
        $this->_designerHelper = $designerHelper;
    }

    /**
     * @return \Ss\Collection\Model\ResourceModel\Collection\Collection
     */
    public function getCollectionData()
    {
        // Check if posts has already been defined
        // makes our block nice and re-usable! We could
        // pass the 'posts' data to this block, with a collection
        // that has been filtered differently!
        if (!$this->hasData('collections')) {
            $posts = $this->_collectionCollectionFactory
                ->create()
            ;
            $this->setData('collections', $posts);
        }
        return $this->getData('collections');
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Ss\Collection\Model\Collection::CACHE_TAG . '_' . 'list'];
    }

    /**
     * Get Title
     * @return string
     */
    public function getCollectionIds()
    {
        return $this->getData('collection_ids');
    }
    
    /**
     * loadCollection by Id
     * @param int $collectionId
     * @return object|boolean
     */
    public function loadCollection($collectionId)
    {
        if (!isset($collectionId) || empty($collectionId)) {
            return false;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('Ss\Collection\Model\Collection')->load($collectionId);

    }

    /**
     * Get Title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }
    
    /**
     * Get Match Now Title
     * @return string
     */
    public function getMatchNowTitle()
    {
        return $this->getData('match_now_title');
    }
    
    /**
     * Get Match Now Url
     * @return string
     */
    public function getMatchNowUrl()
    {
        return $this->getData('match_now_url');
    }
    
    /**
     * Get Shop Now Title
     * @return string
     */
    public function getShopNowTitle()
    {
        return $this->getData('shop_now_title');
    }
    
    /**
     * Get Shop Now Url
     * @return string
     */
    public function getShopNowUrl()
    {
        return $this->getData('show_now_url');
    }
    
    /**
     * Get Collection Url
     * @return string
     */
    public function getCollectionUrl()
    {
        return $this->getData('collection_url');
    }
    
    /**
     * Get Price Description
     * @return string
     */
    public function getPriceDescription()
    {
        return $this->getData('price_description');
    }
    
    /**
     * Get Quantity Description
     * @return string
     */
    public function getQuantityDescription()
    {
        return $this->getData('quantity_description');
    }
    
    /**
     * Get source image url
     * @return string
     */
    public function getSrcMediaImage()
    {
        return $this->_designerHelper->getSrcMediaImage();
    }
}

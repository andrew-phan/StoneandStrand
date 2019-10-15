<?php

namespace Ss\Collection\Block\Widget;

class CollectionMenu extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Ashsmith\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    protected $_collectionCollectionFactory;
    protected $_designerHelper;
    protected $_collectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Collection\Model\ResourceModel\Collection\CollectionFactory $collectionCollectionFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Ss\Collection\Model\ResourceModel\Collection\CollectionFactory $collectionCollectionFactory, \Ss\Designer\Helper\Data $designerHelper, \Ss\Collection\Model\CollectionFactory $collectionFactory, array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_collectionCollectionFactory = $collectionCollectionFactory;
        $this->_designerHelper = $designerHelper;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Ss\Collection\Model\Collection::CACHE_TAG . '_' . 'menu'];
    }

    /**
     * Get Title
     * @return string
     */
    public function getCollectionIds()
    {
        $listIds = explode(',', $this->getData('collection_ids'));
        return $listIds;
    }

    /**
     * loadCollection by Id
     * @param int $collectionId
     * @return object|boolean
     */
    public function getCollections()
    {
        $results = [];
        $collectionIds = $this->getCollectionIds();
        
        if (!empty($collectionIds)) {
            $limit = \Ss\Collection\Model\Collection::LIMIT_ITEM_MENU;
            $results = $this->_collectionCollectionFactory->create()
                    ->addFieldToFilter('collection_id', ['IN' => $collectionIds])
                    ->setPageSize($limit)->setOrder('name', 'ASC');
        }
        
        return $results;
    }

    /**
     * Get source image url
     * @return string
     */
    public function getSrcMediaImage()
    {
        return $this->_designerHelper->getSrcMediaImage();
    }

    /**
     * * @todo To get column number widget
     * @return type
     */
    public function getColumnNumber()
    {
        return $this->getData('column_number');
    }
    
    /**
     * @todo To limit String
     * @param type $string
     * @param type $limit
     * @return type
     */
    public function limitString($string, $limit = 100)
    {
        // Return early if the string is already shorter than the limit
        if (strlen($string) < $limit) {
            return $string;
        }
        $regex = "/(.{1,$limit})\b/";
        preg_match($regex, $string, $matches);
        return $matches[1];
    }

}

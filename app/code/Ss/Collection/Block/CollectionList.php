<?php

namespace Ss\Collection\Block;

class CollectionList extends \Magento\Framework\View\Element\Template implements
\Magento\Framework\DataObject\IdentityInterface
{

    /**
     * @var \Ashsmith\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    protected $_collectionCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Collection\Model\ResourceModel\Collection\CollectionFactory $collectionCollectionFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Ss\Collection\Model\ResourceModel\Collection\CollectionFactory $collectionCollectionFactory, array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_collectionCollectionFactory = $collectionCollectionFactory;
    }

    /**
     * @return \Ss\Collection\Model\ResourceModel\Collection\Collection
     */
    public function getCollectionCollection()
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

}

<?php

namespace Ss\Designer\Block;

class DesignerList extends \Magento\Framework\View\Element\Template implements
\Magento\Framework\DataObject\IdentityInterface
{

    /**
     * @var \Ashsmith\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    protected $_designerCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Designer\Model\ResourceModel\Designer\CollectionFactory $designerCollectionFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
        \Ss\Designer\Model\ResourceModel\Designer\CollectionFactory $designerCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_designerCollectionFactory = $designerCollectionFactory;
    }

    /**
     * @return \Ss\Designer\Model\ResourceModel\Designer\Collection
     */
    public function getDesignerCollection()
    {
        // Check if posts has already been defined
        // makes our block nice and re-usable! We could
        // pass the 'posts' data to this block, with a collection
        // that has been filtered differently!
        if (!$this->hasData('designers')) {
            $posts = $this->_designerCollectionFactory
                ->create()
            ;
            $this->setData('designers', $posts);
        }
        return $this->getData('designers');
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Ss\Designer\Model\Designer::CACHE_TAG . '_' . 'list'];
    }

}

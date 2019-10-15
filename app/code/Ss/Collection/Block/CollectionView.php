<?php

namespace Ss\Collection\Block;

class CollectionView extends \Magento\Catalog\Block\Product\AbstractProduct implements
\Magento\Framework\DataObject\IdentityInterface
{

    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

    /**
     * Construct
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Collection\Model\Collection $collection
     * @param \Ss\Collection\Model\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Ss\Collection\Model\Collection $collection, \Ss\Collection\Model\CollectionFactory $collectionFactory, array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_collection = $collection;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @return \Ashsmith\Blog\Model\Post
     */
    public function getCollection()
    {
        // Check if posts has already been defined
        // makes our block nice and re-usable! We could
        // pass the 'posts' data to this block, with a collection
        // that has been filtered differently!
        if (!$this->hasData('collection')) {
            if ($this->getCollectionId()) {
                /** @var \Ss\Collection\Model\Collection $page */
                $post = $this->_collectionFactory->create();
            } else {
                $post = $this->_collection;
            }
            $this->setData('collection', $post);
        }

        return $this->getData('collection');
    }

    /**
     * @todo To get list product follow collection ID.
     * @return type
     */
    public function getListProductCollections()
    {
        return $this->getCollection()->getListProduct();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getListProductCollections();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection', ['collection' => $collection]
        );

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        return $block;
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Ss\Collection\Model\Collection::CACHE_TAG . '_' . $this->getCollection()->getId()];
    }

}

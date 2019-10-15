<?php

namespace Ss\Collection\Controller\Generate;

use \Magento\Framework\App\Action\Action;

/**
 * Generate url rewrite Designer
 */
class Index extends Action
{

    protected $_collectionFactory;
    protected $_collectionResource;

    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Ss\Collection\Model\ResourceModel\Collection\CollectionFactory $collectionFactory,
        \Ss\Collection\Model\ResourceModel\Collection $collectionResource
    )
    {
        parent::__construct($context);
        $this->_collectionFactory = $collectionFactory;
        $this->_collectionResource = $collectionResource;
    }

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {

        $collection = $this->_collectionFactory->create();
        foreach ($collection as $item) {
            // Update option value
            $this->_collectionResource->_saveOptionId($item);
        }
        die("done");
    }

}

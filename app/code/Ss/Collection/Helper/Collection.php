<?php

namespace Ss\Collection\Helper;

use Magento\Framework\App\Action\Action;

class Collection extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Ss\Collection\Model\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Ss\Collection\Model\Collection $collection
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Ss\Collection\Model\Collection $collection, \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->_collection = $collection;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Return a blog post from given post id.
     *
     * @param Action $action
     * @param null $collectionId
     * @return \Magento\Framework\View\Result\Page|bool
     */
    public function prepareResultCollection(Action $action, $collectionId = null)
    {
        if ($collectionId !== null && $collectionId !== $this->_collection->getId()) {
            $delimiterPosition = strrpos($collectionId, '|');
            if ($delimiterPosition) {
                $collectionId = substr($collectionId, 0, $delimiterPosition);
            }

            if (!$this->_collection->load($collectionId)) {
                return false;
            }
        }

        if (!$this->_collection->getId()) {
            return false;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        // We can add our own custom page handles for layout easily.
        $resultPage->addHandle('collection_view_index');

        // This will generate a layout handle like: blog_post_view_id_1
        // giving us a unique handle to target specific blog posts if we wish to.
        $resultPage->addPageLayoutHandles(['id' => $this->_collection->getId()]);


        $resultPage->getConfig()->getTitle()->set('Collection | ' . $this->_collection->getName());

        return $resultPage;
    }

}

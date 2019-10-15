<?php

namespace Ss\Collection\Controller\View;

use \Magento\Framework\App\Action\Action;

class Index extends Action
{

    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
    )
    {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $collection_id = $this->getRequest()->getParam('collection_id', $this->getRequest()->getParam('id', false));

        $post_helper = $this->_objectManager->get('Ss\Collection\Helper\Collection');
        $result_page = $post_helper->prepareResultCollection($this, $collection_id);
        if (!$result_page) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        return $result_page;
    }

}

<?php

namespace Ss\Collection\Controller\Adminhtml\Collection;

/**
 * Index Collection.
 */
class Index extends \Ss\Collection\Controller\Adminhtml\Collection
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->forward('grid');

            return $resultForward;
        }

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }

}

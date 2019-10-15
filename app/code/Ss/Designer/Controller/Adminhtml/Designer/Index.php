<?php

namespace Ss\Designer\Controller\Adminhtml\Designer;

/**
 * Index Designer.
 */
class Index extends \Ss\Designer\Controller\Adminhtml\Designer
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

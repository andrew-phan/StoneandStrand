<?php

namespace Ss\Slideshow\Controller\Adminhtml\Banner;

/**
 * Index banner.
 */
class Index extends \Ss\Slideshow\Controller\Adminhtml\Banner
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

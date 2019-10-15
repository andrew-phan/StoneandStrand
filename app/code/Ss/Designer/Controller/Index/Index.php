<?php

namespace Ss\Designer\Controller\Index;

use \Magento\Framework\App\Action\Action;

class Index extends Action
{

    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;
    protected $_themeHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Ss\Theme\Helper\Data $themeHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_themeHelper = $themeHelper;
        parent::__construct($context);
    }

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->setMetadata('keywords', $this->_themeHelper->getSystemConfig(\Ss\Theme\Helper\Data::XML_PATH_DESIGNER_KEYWORD));
        $resultPage->getConfig()->setMetadata('description', $this->_themeHelper->getSystemConfig(\Ss\Theme\Helper\Data::XML_PATH_DESIGNER_DESCRIPTION));
        $resultPage->getConfig()->getTitle()->set($this->_themeHelper->getSystemConfig(\Ss\Theme\Helper\Data::XML_PATH_DESIGNER_PAGE_TITLE));
        return $resultPage;
    }

}

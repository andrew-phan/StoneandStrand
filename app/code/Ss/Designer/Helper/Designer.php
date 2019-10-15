<?php

namespace Ss\Designer\Helper;

use Magento\Framework\App\Action\Action;

/**
 * Helper Designer.
 */
class Designer extends \Magento\Framework\App\Helper\AbstractHelper
{

    const SECURITY_PROTOCOL = 'https://';
    const UNSECURITY_PROTOCOL = 'http://';
    
    /**
     * @var \Ss\Designer\Model\Designer
     */
    protected $_designer;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Ss\Designer\Model\Designer $designer
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
        \Ss\Designer\Model\Designer $designer,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->_designer = $designer;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Return a blog post from given post id.
     *
     * @param Action $action
     * @param null $designerId
     * @return \Magento\Framework\View\Result\Page|bool
     */
    public function prepareResultDesigner(Action $action,
        $designerId = null)
    {
        if ($designerId !== null && $designerId !== $this->_designer->getId()) {
            $delimiterPosition = strrpos($designerId, '|');
            if ($delimiterPosition) {
                $designerId = substr($designerId, 0, $delimiterPosition);
            }

            if (!$this->_designer->load($designerId)) {
                return false;
            }
        }

        if (!$this->_designer->getId()) {
            return false;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        // We can add our own custom page handles for layout easily.
        $resultPage->addHandle(\Ss\Designer\Model\Designer::PREFIX_URL_DESIGNER . '_view_index');

        // This will generate a layout handle like: blog_post_view_id_1
        // giving us a unique handle to target specific blog posts if we wish to.
        $resultPage->addPageLayoutHandles(['designer_id' => $this->_designer->getId()]);

        return $resultPage;
    }

}

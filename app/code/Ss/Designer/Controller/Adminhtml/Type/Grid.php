<?php

namespace Ss\Designer\Controller\Adminhtml\Type;

class Grid extends \Ss\Designer\Controller\Adminhtml\Type
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();

        return $resultLayout;
    }

}

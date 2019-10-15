<?php

namespace Ss\Designer\Controller\Adminhtml\Designer;

/**
 * Banners of slider action
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Tags extends \Ss\Designer\Controller\Adminhtml\Designer
{

    /**
     * 
     * @return type
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('designer.designer.edit.tab.tags')
            ->setInTag($this->getRequest()->getPost('tags', null));

        return $resultLayout;
    }

}

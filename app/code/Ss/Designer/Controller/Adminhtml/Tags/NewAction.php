<?php

namespace Ss\Designer\Controller\Adminhtml\Tags;

/**
 * NewAction
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class NewAction extends \Ss\Designer\Controller\Adminhtml\Tags
{

    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }

}

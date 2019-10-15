<?php

namespace Ss\Designer\Controller\Adminhtml\Type;

/**
 * NewAction
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class NewAction extends \Ss\Designer\Controller\Adminhtml\Type
{

    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }

}

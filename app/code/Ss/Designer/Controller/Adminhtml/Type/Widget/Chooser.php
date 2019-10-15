<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Controller\Adminhtml\Type\Widget;

class Chooser extends \Magento\Backend\App\Action
{

    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $typeGrid = $this->_view->getLayout()->createBlock(
            'Ss\Designer\Block\Adminhtml\Widget\Type\Chooser', '', ['data' => []]
        );
        $html = $typeGrid->toHtml();

        $this->getResponse()->setBody($html);
    }

}

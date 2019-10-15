<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Controller\Adminhtml\Designer\Widget;

/**
 * Chooser widget controller
 */
class Chooser extends \Magento\Backend\App\Action
{

    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $designerGrid = $this->_view->getLayout()->createBlock(
            'Ss\Designer\Block\Adminhtml\Widget\Designer\Chooser', '', ['data' => []]
        );
        $html = $designerGrid->toHtml();

        $this->getResponse()->setBody($html);
    }

}

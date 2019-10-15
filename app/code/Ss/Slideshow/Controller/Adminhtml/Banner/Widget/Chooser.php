<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Slideshow\Controller\Adminhtml\Banner\Widget;

class Chooser extends \Magento\Backend\App\Action
{

    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $bannersGrid = $this->_view->getLayout()->createBlock(
            'Ss\Slideshow\Block\Adminhtml\Widget\Chooser', '', ['data' => []]
        );
        $html = $bannersGrid->toHtml();

        $this->getResponse()->setBody($html);
    }

}

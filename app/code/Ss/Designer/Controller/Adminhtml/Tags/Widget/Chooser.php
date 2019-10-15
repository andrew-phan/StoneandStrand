<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Controller\Adminhtml\Tags\Widget;

class Chooser extends \Magento\Backend\App\Action
{

    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $tagGrid = $this->_view->getLayout()->createBlock(
            'Ss\Designer\Block\Adminhtml\Widget\Tag\Chooser', '', ['data' => []]
        );
        $html = $tagGrid->toHtml();

        $this->getResponse()->setBody($html);
    }

}

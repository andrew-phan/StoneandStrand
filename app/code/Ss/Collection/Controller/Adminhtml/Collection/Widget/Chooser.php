<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Collection\Controller\Adminhtml\Collection\Widget;

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
        $collectionGrid = $this->_view->getLayout()->createBlock(
            'Ss\Collection\Block\Adminhtml\Widget\Collection\Chooser', '', ['data' => []]
        );
        $html = $collectionGrid->toHtml();

        $this->getResponse()->setBody($html);
    }

}

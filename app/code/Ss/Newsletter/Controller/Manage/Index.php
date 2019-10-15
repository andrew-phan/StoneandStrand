<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Newsletter\Controller\Manage;

class Index extends \Magento\Newsletter\Controller\Manage
{
    /**
     * Managing newsletter subscription page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();

        if ($block = $this->_view->getLayout()->getBlock('customer_newsletter')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->getPage()->getConfig()->getTitle()->set(__('My Preferences'));
        $this->_view->renderLayout();
    }
}

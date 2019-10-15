<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Customer\Override\Controller\Account;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Create extends \Magento\Customer\Controller\Account\Create
{

    /**
     * Customer register form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            $resultRedirect->setPath('*/*');
        } else {
            $resultRedirect->setPath('*/*/login');
        }
        
        return $resultRedirect;
    }
}

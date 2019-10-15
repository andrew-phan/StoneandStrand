<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Rma\Controller\Returns;

class Success extends \Magento\Rma\Controller\Returns
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->session = $customerSession;
        parent::__construct($context, $coreRegistry);
    }
    
    /**
     * RMA view page
     *
     * @return void
     */
    public function execute()
    {
        $orderId = $this->session->getLastReturnId();
        
        if (empty($orderId)) {
            $this->_redirect('sales/order/history');
            return;
        }
        
        $this->session->unsLastReturnId();
        
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        
        $this->_coreRegistry->register('current_order', $order);
        
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Return Success'));
        
        $this->_view->renderLayout();
    }
}

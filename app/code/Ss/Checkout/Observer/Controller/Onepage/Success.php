<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Checkout\Observer\Controller\Onepage;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Success implements ObserverInterface
{

    /**
     * @var OrderFactory
     */
    protected $orderFactory;
    
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param OrderFactory $orderFactory
     */
    public function __construct(Registry $coreRegistry, OrderFactory $orderFactory, StoreManagerInterface $storeManager)
    {
        $this->_coreRegistry = $coreRegistry;
        $this->orderFactory = $orderFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Add object order into success page.
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        
        if (empty($orderIds) || !is_array($orderIds)) {
            return $this;
        }
        
        //Save current order to register
        $order = $this->orderFactory->create()->load($orderIds);
        $this->_coreRegistry->register('current_order', $order);
        $this->_coreRegistry->register('facebook_pixel', $this->getFacebookPixel($order));

        return $this;
    }
    
    public function getFacebookPixel($order)
    {
        $orderPrice = number_format($order->getGrandTotal(), 2);
        $currentCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencyCode();
        
        $script = "fbq('track', 'Purchase', {value: '$orderPrice', currency: '$currentCode'})";
        return $script;
    }
}

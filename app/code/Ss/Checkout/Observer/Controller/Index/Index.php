<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Checkout\Observer\Controller\Index;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Index implements ObserverInterface
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param OrderFactory $orderFactory
     */
    public function __construct(Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Add object order into success page.
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $this->_coreRegistry->register('facebook_pixel', $this->getFacebookPixel());

        return $this;
    }
    
    public function getFacebookPixel()
    {
        $script = "fbq('track', 'InitiateCheckout')";
        return $script;
    }
}

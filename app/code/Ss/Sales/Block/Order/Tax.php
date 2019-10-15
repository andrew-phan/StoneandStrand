<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tax totals modification block. Can be used just as subblock of \Magento\Sales\Block\Order\Totals
 */
namespace Ss\Sales\Block\Order;

use Magento\Sales\Model\Order;

class Tax extends \Magento\Tax\Block\Sales\Order\Tax
{
    /**
     * Format total value based on order currency symbol
     *
     * @return  string
     */
    public function currencySymbol()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }
    
    /**
     * Format total value based on order currency code
     *
     * @return  string
     */
    public function currencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencyCode();
    }
}

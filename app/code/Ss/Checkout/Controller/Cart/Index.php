<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Checkout\Controller\Cart;

/**
 * Class checkout_cart_index override
 */
class Index extends \Magento\Checkout\Controller\Cart\Index
{

    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {   
        return $this->_redirect('/');
    }

}

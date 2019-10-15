<?php
/**
 * Order item render block for grouped product type
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Sales\Block\Order\Email\Items\Order;

use Ss\Sales\Block\Order\Email\Items\Order\DefaultOrder;

class Grouped extends DefaultOrder
{
    const TEMPLATE = 'Ss_Sales::email/items/order/default.phtml';

    public function getTemplate() {
        return static::TEMPLATE;
    }

}

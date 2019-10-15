<?php
/**
 * Order item render block for grouped product type
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Sales\Block\Order\Item\Renderer;

use Ss\Sales\Block\Order\Item\Renderer\DefaultRenderer;

class Grouped extends DefaultRenderer
{
    const TEMPLATE = 'Ss_Sales::order/items/renderer/default.phtml'; // hardcode template render item
    
    public function getTemplate() {
        return static::TEMPLATE;
    }

        /**
     * Prepare item html
     *
     * This method uses renderer for real product type
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getItem()->getOrderItem()) {
            $item = $this->getItem()->getOrderItem();
        } else {
            $item = $this->getItem();
        }
        if ($productType = $item->getRealProductType()) {
            $renderer = $this->getRenderedBlock()->getItemRenderer($productType);
            $renderer->setItem($this->getItem());
            return $renderer->toHtml();
        }
        return parent::_toHtml();
    }
}

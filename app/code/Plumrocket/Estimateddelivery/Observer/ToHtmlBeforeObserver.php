<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Estimateddelivery
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Estimateddelivery\Observer;

use Plumrocket\Estimateddelivery\Model\Config\Source\Position;

class ToHtmlBeforeObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_helper;
    protected $_productHelper;
    protected $_request;
    protected $_layout;
    protected $_productModel;

    public function __construct(
        \Plumrocket\Estimateddelivery\Helper\Data $helper,
        \Plumrocket\Estimateddelivery\Helper\Product $productHelper,
        \Magento\Framework\App\RequestInterface $httpRequest,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Catalog\Model\ProductFactory $productModel
    ) {
        $this->_helper = $helper;
        $this->_productHelper = $productHelper;
        $this->_request = $httpRequest;
        $this->_layout = $layout;
        $this->_productModel = $productModel;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getBlock();

        if(!$this->_helper->moduleEnabled()) {
            return;
        }

        switch (true) {
            case $this->_helper->showPosition(Position::SHOPPING_CART)
                && $block instanceof \Magento\Checkout\Block\Cart\Item\Renderer
                && $this->_request->getModuleName()        == 'checkout'
                && $this->_request->getControllerName()    == 'cart':

                if($item = $block->getItem()) {
                    if($options = $this->_getEstimatedDeliveryOptions($item)) {
                        $item->addOption([
                            'code' => 'additional_options',
                            'value' => serialize($options)
                        ]);
                    }
                }
                break;

            case $this->_helper->showPosition(Position::PM_ORDER_SUCCESS)
                && $block instanceof \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
                && $this->_request->getModuleName()        == 'checkout'
                && $this->_request->getControllerName()    == 'onepage'
                && $this->_request->getActionName()        == 'success':
            /*case $this->_helper->showPosition(Position::PM_ORDER_SUCCESS)
                && $block instanceof \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder
                && $this->_request->getModuleName()        == 'checkoutspage'
                && $this->_request->getControllerName()    == 'preview'
                && $this->_request->getActionName()        == 'email':*/
            case $this->_helper->showPosition(Position::CUSTOMER_ORDER)
                && $block instanceof \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
                && $this->_request->getModuleName()        == 'sales'
                && $this->_request->getControllerName()    == 'order'
                // Fix when html was escaped on print order page.
                && ($this->_request->getActionName()        != 'print'
                    || $this->_request->getActionName()     == 'print' && $block->setPrintStatus(false)):
            case $this->_helper->showPosition(Position::ORDER_CONFIRMATION)
                && $block instanceof \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder
                && $block->getRenderedBlock() instanceof \Magento\Sales\Block\Order\Email\Items:
            case $this->_helper->showPosition(Position::INVOICE)
                && $block instanceof \Magento\Sales\Block\Order\Email\Items\DefaultItems
                && $block->getRenderedBlock() instanceof \Magento\Sales\Block\Order\Email\Invoice\Items
                && $item = $block->getItem()->getOrderItem():
            case $this->_helper->showPosition(Position::SHIPMENT)
                && $block instanceof \Magento\Sales\Block\Order\Email\Items\DefaultItems
                && $block->getRenderedBlock() instanceof \Magento\Sales\Block\Order\Email\Shipment\Items
                && $item = $block->getItem()->getOrderItem():
            case $this->_helper->showPosition(Position::ADMINPANEL_ORDER)
                && $block instanceof \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer // \Magento\Sales\Block\Adminhtml\Items\AbstractItems
                && $this->_request->getControllerName()    == 'order'
                && $this->_request->getActionName()        == 'view':

                if(empty($item)) {
                    $item = $block->getItem();
                }

                if($item) {
                    if($options = $this->_getEstimatedDeliveryOptions($item)) {

                        $itemOptions = $item->getProductOptions();

                        if(null === $itemOptions) {
                            $itemOptions = [];
                        }

                        if (!is_array($itemOptions)) {
                            if($itemOptions = @unserialize($itemOptions)) {
                                $doSerialize = true;
                            }
                        }

                        // If use key "additional_options", delivery data will display before configurable attributes of product.
                        if(empty($itemOptions['attributes_info'])) {
                            $itemOptions['attributes_info'] = [];
                        }

                        $itemOptions['attributes_info'] = array_merge($itemOptions['attributes_info'], $options);
                        if(!empty($doSerialize)) {
                            $itemOptions = serialize($itemOptions);
                        }
                        $item->setProductOptions($itemOptions);
                    }
                }
                break;
        }
    }

    protected function _getEstimatedDeliveryOptions($item)
    {
        $options = [];

        if(!$product = $item->getProduct()) {
            $product = $this->_productModel->create()->load($item->getProductId());
        }

        $estimateddelivery = $this->_productHelper
            ->setProduct($product);

        if ($delivery = $estimateddelivery->getDelivery()) {
            $options[] = $delivery;
        }

        if ($shipping = $estimateddelivery->getShipping()) {
            $options[] = $shipping;
        }

        return $options;
    }
}

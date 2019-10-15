<?php

namespace Ss\Checkout\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Add custom field html on order admin
 */
class AddHtmlToOrderShippingViewObserver implements ObserverInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager)
    {
        $this->_objectManager = $objectmanager;
    }

    /**
     * 
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        if ($observer->getElementName() == 'order_info' || $observer->getElementName() == 'order_shipping_view') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock('order_shipping_view');
            if ($orderShippingViewBlock && $orderShippingViewBlock->getOrder()) {
                $order = $orderShippingViewBlock->getOrder();
                $reasonBuyBlock = $this->_objectManager->create('Magento\Framework\View\Element\Template');

                $template = "";
                switch ($observer->getElementName()) {
                    case "order_info":
                        $template = 'Ss_Checkout::order_reason_buy_info.phtml';
                        $reasonBuyBlock->setSsReasonBuy($order->getSsReasonBuy());
                        break;
                    case "order_shipping_view":
                        $template = 'Ss_Checkout::order_shipping_date_info.phtml';
                        $reasonBuyBlock->setSsShippingDate($order->getSsShippingDate());
                        break;
                    default:
                }

                $reasonBuyBlock->setTemplate($template);
                $html = $observer->getTransport()->getOutput() . $reasonBuyBlock->toHtml();
                $observer->getTransport()->setOutput($html);
            }
        }
    }

}

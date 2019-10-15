<?php

namespace Ss\Checkout\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class save custom field to Order
 */
class SaveCustomFieldToOrderObserver implements ObserverInterface
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
     * @todo execute event sales_model_service_quote_submit_before
     * @param EventObserver $observer
     * @return \Ss\Checkout\Model\Observer\SaveCustomFieldToOrderObserver
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $quoteRepository->get($order->getQuoteId());
        $order->setSsReasonBuy($quote->getSsReasonBuy());

        if ($quote->getSsShippingDate()) {
            $order->setSsShippingDate($quote->getSsShippingDate());
        }
        return $this;
    }

}

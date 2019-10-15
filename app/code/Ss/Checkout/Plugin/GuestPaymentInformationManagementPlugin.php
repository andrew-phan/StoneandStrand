<?php

/**
 * Copyright © 2015 Customer Paradigm. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Checkout\Plugin;

/**
 * Class save order comment
 */
class GuestPaymentInformationManagementPlugin
{

    /** @var \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory */
    protected $historyFactory;

    /** @var \Magento\Sales\Model\OrderFactory $orderFactory */
    protected $orderFactory;
    protected $_sessionManager;
    protected $_subscriberFactory;

    /**
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
    \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Session\SessionManager $session,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    )
    {
        $this->historyFactory = $historyFactory;
        $this->orderFactory = $orderFactory;
        $this->_sessionManager = $session;
        $this->_subscriberFactory = $subscriberFactory;
    }

    /**
     * @param \Closure $proceed
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     *
     * @return int $orderId
     */
    public function aroundSavePaymentInformationAndPlaceOrder(\Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    )
    {
        /** @param string $comment */
        $comment = NULL;
        // get JSON post data
        $request_body = file_get_contents('php://input');
        // decode JSON post data into array
        $data = json_decode($request_body, true);
        // get order comments
        if (isset($data['paymentMethod']['additional_data']['comments'])) {
            // make sure there is a comment to save
            $orderComments = $data['paymentMethod']['additional_data']['comments'];
            if ($orderComments && !empty($orderComments)) {
                // remove any HTML tags
                $comment = strip_tags($comment);
                $comment = 'ORDER COMMENT:  ' . $orderComments;
            }
        }
        // run parent method and capture int $orderId
        $orderId = $proceed($cartId, $email, $paymentMethod, $billingAddress);
        // if $comments
        if ($comment) {
            /** @param \Magento\Sales\Model\OrderFactory $order */
            $order = $this->orderFactory->create()->load($orderId);
            // make sure $order exists 
            if ($order->getData('entity_id')) {
                /** @param string $status */
                $status = $order->getData('status');
                /** @param \Magento\Sales\Model\Order\Status\HistoryFactory $history */
                $history = $this->historyFactory->create();
                // set comment history data
                $history->setData('comment', $comment);
                $history->setData('parent_id', $orderId);
                $history->setData('is_visible_on_front', 1);
                $history->setData('is_customer_notified', 0);
                $history->setData('entity_name', 'order');
                $history->setData('status', $status);
                $history->save();
            }
        }

        // check is Subcripber
        if ($this->_sessionManager->getIsSubscribe()) {
            $email = $this->_sessionManager->getSsEmail();
            $this->_subscriberFactory->create()->subscribe($email);
        }
        return $orderId;
    }

}

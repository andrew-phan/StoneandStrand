<?php

namespace Ss\Checkout\Plugin;

/**
 * Class Event Shipping information
 */
class ShippingInformationManagementPlugin
{

    protected $quoteRepository;
    protected $_sessionManager;
    protected $_customerSession;
    protected $_customerAddress;

    /**
     *
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Framework\Session\SessionManager $session
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Address $customerAddress
     */
    public function __construct(
    \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\Session\SessionManager $session,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Address $customerAddress
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->_sessionManager = $session;
        $this->_customerSession = $customerSession;
        $this->_customerAddress = $customerAddress;
    }

    /**
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
    \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $reasonBuy = $extAttributes->getSsReasonBuy();
        $quote = $this->quoteRepository->getActive($cartId);
        // Save reason buy to quote
        $quote->setSsReasonBuy($reasonBuy);

        // Save shipping date to quote
        if ($extAttributes->getSsShippingDate()) {
            $productInformation = $extAttributes->getSsShippingDate();
            $quote->setSsShippingDate($productInformation);
        }

        // Save is Subscribe to session.
        $this->_sessionManager->start();
        $this->_sessionManager->setIsSubscribe($extAttributes->getSsNewsletter());
        $this->_sessionManager->setSsEmail($extAttributes->getSsEmail());
        $this->_sessionManager->setSsSaveDefaultAddress($extAttributes->getSsSaveDefaultAddress());

        // Check save default address
        if ($extAttributes->getSsSaveDefaultAddress() && $this->_customerSession->isLoggedIn()) {
            $shippingAddress = $addressInformation->getShippingAddress();
            // set default shipping address when exist address.
            if ($shippingAddress->getCustomerAddressId()) {
                $this->_customerAddress->load($shippingAddress->getCustomerAddressId());
                $this->_customerAddress->setIsDefaultShipping(TRUE);
                $this->_customerAddress->save();
            }
        }
    }

}

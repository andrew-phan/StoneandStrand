<?php

/**
 * Ebizmarts_MAgeMonkey Magento JS component
 *
 * @category    Ebizmarts
 * @package     Ebizmarts_MageMonkey
 * @author      Ebizmarts Team <info@ebizmarts.com>
 * @copyright   Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ebizmarts\MageMonkey\Model\Plugin;

class Subscriber
{

    /**
     * @var \Ebizmarts\MageMonkey\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Ebizmarts\MageMonkey\Helper\Data $helper
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Session $customerSession
     */
    protected $_api = null;

    public function __construct(
    \Ebizmarts\MageMonkey\Helper\Data $helper, \Magento\Customer\Model\Customer $customer, \Magento\Customer\Model\Session $customerSession, \Ebizmarts\MageMonkey\Model\Api $api
    )
    {
        $this->_helper = $helper;
        $this->_customer = $customer;
        $this->_customerSession = $customerSession;
        $this->_api = $api;
    }

    public function beforeUnsubscribeCustomerById(
    $subscriber, $customerId
    )
    {

        $isEssential = (boolean) $this->_customerSession->getIsEssential();
        $isEditorial = (boolean) $this->_customerSession->getIsEditorial();
        $isSalePromotion = (boolean) $this->_customerSession->getIsSalePromotion();
        $subscriber->loadByCustomerId($customerId);
        if ($subscriber->getMagemonkeyId()) {
            if (!$isEssential) {
                $this->_api->listDeleteMember($this->_helper->getEssentialList(), $subscriber->getMagemonkeyId());
            }
            if (!$isEditorial) {
                $this->_api->listDeleteMember($this->_helper->getEditorialList(), $subscriber->getMagemonkeyId());
            }
            if (!$isSalePromotion) {
                $this->_api->listDeleteMember($this->_helper->getDefaultList(), $subscriber->getMagemonkeyId());
            }
            $subscriber->setMagemonkeyId('');
            return [$customerId];
        }
    }

    public function beforeSubscribeCustomerById(
    $subscriber, $customerId
    )
    {
        $isEssential = (boolean) $this->_customerSession->getIsEssential();
        $isEditorial = (boolean) $this->_customerSession->getIsEditorial();
        $isSalePromotion = (boolean) $this->_customerSession->getIsSalePromotion();
        
        $subscriber->loadByCustomerId($customerId);
        $storeId = $subscriber->getStoreId();
        if ($this->_helper->isMonkeyEnabled($storeId)) {
            $customer = $this->_customer->load($customerId);
            $mergeVars = $this->_helper->getMergeVars($customer);
            $api = $this->_api;
            $isSubscribeOwnEmail = $this->_customerSession->isLoggedIn() && $this->_customerSession->getCustomerDataObject()->getEmail() == $subscriber->getSubscriberEmail();
            if ($this->_helper->isDoubleOptInEnabled($storeId) && !$isSubscribeOwnEmail) {
                $status = 'pending';
            } else {
                $status = 'subscribed';
            }
            $data = array(
                'email_address' => $customer->getEmail(),
                'email_type' => 'html',
                'status' => $status);
            if ($mergeVars) {
                $data['merge_fields'] = $mergeVars;
            }
            if (!isset($customer)) {
                $data['email_address'] = $customerId;
            }
            $return = array();
            if ($isEssential) {
                $data['list_id'] = $this->_helper->getEssentialList();           
                $return[] = $api->listCreateMember($this->_helper->getEssentialList(), json_encode($data));
            }
        
            if ($isEditorial) {
                $data['list_id'] = $this->_helper->getEditorialList();
                $return[] = $api->listCreateMember($this->_helper->getEditorialList(), json_encode($data));
            }
            if ($isSalePromotion) {
                $data['list_id'] = $this->_helper->getDefaultList();            
                $return[] = $api->listCreateMember($this->_helper->getDefaultList(), json_encode($data));
            }
            
            if (count($return)) {
            
                foreach ($return as $val) {
                    if (isset($val->id)) {
                        $subscriber->setMagemonkeyId($val->id);
                    }
                    
                }
            }
            
        }
        
        return [$customerId];
    }

    public function beforeSubscribe($subscriber, $customerId)
    {

        $storeId = $subscriber->getStoreId();
        $subscriber->getSubscriberEmail();
        $customer = $this->_customer->load($customerId);

        $api = $this->_api;
        $isSubscribeOwnEmail = $this->_customerSession->isLoggedIn() && $this->_customerSession->getCustomerDataObject()->getEmail() == $subscriber->getSubscriberEmail();
        if ($this->_helper->isDoubleOptInEnabled($storeId) && !$isSubscribeOwnEmail) {
            $status = 'pending';
        } else {
            $status = 'subscribed';
        }
        if ($this->_helper->isMonkeyEnabled($storeId) && (!isset($customer) || $customer->getEmail() == '')) {
            $data = array(
                'list_id' => $this->_helper->getDefaultList(),
                'email_address' => $customerId,
                'email_type' => 'html',
                'status' => $status);
            $return[] = $api->listCreateMember($this->_helper->getDefaultList(), json_encode($data));

            $isEssential = (boolean) $this->_customerSession->getIsEssential() ? $this->_customerSession->getIsEssential() : true;
            $isEditorial = (boolean) $this->_customerSession->getIsEditorial() ? $this->_customerSession->getIsEditorial() : true;

            if ($isEssential) {
                $data['list_id'] = $this->_helper->getEssentialList();           
                $return[] = $api->listCreateMember($this->_helper->getEssentialList(), json_encode($data));
            }

            if ($isEditorial) {
                $data['list_id'] = $this->_helper->getEditorialList();
                $return[] = $api->listCreateMember($this->_helper->getEditorialList(), json_encode($data));
            }

            foreach ($return as $val) {
                if (isset($val->id)) {
                    $subscriber->setMagemonkeyId($val->id);
                }
            }
        }

        return [$customerId];
    }

}

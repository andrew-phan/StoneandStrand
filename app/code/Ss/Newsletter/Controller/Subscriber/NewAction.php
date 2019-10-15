<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Newsletter\Controller\Subscriber;

use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Model\CustomerFactory;

/**
 * NewAction controller to handle form submit
 */
class NewAction extends \Magento\Newsletter\Controller\Subscriber\NewAction
{

    /**
     * @var CustomerAccountManagement
     */
    protected $customerAccountManagement;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     * @param CustomerAccountManagement $customerAccountManagement
     * @param CustomerRepository $customerRepository
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement,
        CustomerRepository $customerRepository,
        CustomerFactory $customerFactory
    )
    {
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        parent::__construct(
            $context, $subscriberFactory, $customerSession, $storeManager, $customerUrl, $customerAccountManagement
        );
    }

    /**
     * New subscription action
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string) $this->getRequest()->getPost('email');
            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();

                $this->_subscriberFactory->create()->subscribe($email);

                /** @var Customer $customer */
                $customer = $this->customerFactory->create();
                
                $websiteId = $this->_storeManager->getStore()->getWebsiteId();

                $customer->setWebsiteId($websiteId);

                $customer->loadByEmail($email);
                if ($customer->getEmail()) {
                    $this->_save($email);
                }
                
                $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
                $url = $baseUrl . 'page-newsletter-thank-you';
                return $this->getResponse()->setRedirect($url);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addException(
                    $e, __('There was a problem with the subscription: %1', $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong with the subscription.'));
            }
        }

        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }

    /**
     * Save subscription of customer
     * 
     * @param type $email
     */
    private function _save($email)
    {
        $attributeCode = \Ss\Newsletter\Controller\Manage\Save::ATTRIBUTE_CODE_SUBSCRIBER;
        $om = \Magento\Framework\App\ObjectManager::getInstance();

        $customer = $this->customerRepository->get($email);
        $subscriberType = $customer->getCustomAttribute($attributeCode);
        $customer_attributes = $om->get('Magento\Customer\Model\Customer')->load($customer->getId())->getAttribute($attributeCode);
        $options = $customer_attributes->getFrontend()->getSelectOptions();

        $values = array_column($options, 'value');
        $values = array_filter($values);
        $values = implode(',', $values);

        $subscriberType->setValue($values);

        $this->customerRepository->save($customer);
    }
}

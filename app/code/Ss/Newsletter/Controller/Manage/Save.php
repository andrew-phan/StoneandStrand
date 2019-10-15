<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Newsletter\Controller\Manage;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class Save extends \Magento\Newsletter\Controller\Manage\Save
{
    const ATTRIBUTE_CODE_SUBSCRIBER = 'subscriber_type'; // attribute code of subscriber

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory $customerFactory 
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Customer $customer 
     */
    protected $customer;

    /**
     * @var \Magento\Customer\Model\Data\Customer $customerData 
     */
    protected $customerData;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CustomerRepository $customerRepository
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Store\Model\StoreManagerInterface $storeManager, CustomerRepository $customerRepository, \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->subscriberFactory = $subscriberFactory;
        parent::__construct($context, $customerSession, $formKeyValidator, $storeManager, $customerRepository, $subscriberFactory);
    }

    /**
     * Save newsletter subscription preference action
     *
     * @return void|null
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('customer/account/');
        }
        $customerId = $this->_customerSession->getCustomerId();

        if ($customerId === null) {
            $this->messageManager->addError(__('Something went wrong while saving your subscription.'));
        } else {
            try {
                /**
                 * Load post data to save custom attribute/options
                 */
                $essentialValue = (int) $this->getRequest()->getParam('essential_value', 0);
                $editorialValue = (int) $this->getRequest()->getParam('editorial_value', 0);
                $saleValue = (int) $this->getRequest()->getParam('sale_value', 0);

                $isEssentialSubscriber = (boolean) $this->getRequest()->getParam('essential', false);
                $isEditorialSubscriber = (boolean) $this->getRequest()->getParam('editorial', false);
                $isSaleSubscriber = (boolean) $this->getRequest()->getParam('is_subscribed', false);

                $this->_customerSession->setIsEssential($isEssentialSubscriber);
                $this->_customerSession->setIsEditorial($isEditorialSubscriber);
                $this->_customerSession->setIsSalePromotion($isSaleSubscriber);

                $arrayValue = array();
                if ($isEssentialSubscriber) {
                    $arrayValue[] = $essentialValue;
                }
                if ($isEditorialSubscriber) {
                    $arrayValue[] = $editorialValue;
                }
                if ($isSaleSubscriber) {
                    $arrayValue[] = $saleValue;
                }
                $values = implode(',', $arrayValue);

                /**
                 * Load customer data
                 */
                $customer = $this->customerRepository->getById($customerId);
                $storeId = $this->storeManager->getStore()->getId();
                $customer->setStoreId($storeId);

                /**
                 * Store custom values
                 */
                $subscriberType = $customer->getCustomAttribute(static::ATTRIBUTE_CODE_SUBSCRIBER);
                if (!$subscriberType) {
                    $om = \Magento\Framework\App\ObjectManager::getInstance();
                    $subscriberType = $om->get('Magento\Customer\Model\Customer')->load($customer->getId())->getAttribute(static::ATTRIBUTE_CODE_SUBSCRIBER);
                }
                $subscriberType->setValue($values);

                /**
                 * Save customer
                 */
                $this->customerRepository->save($customer);

                if ($isSaleSubscriber) {
                    $this->subscriberFactory->create()->subscribeCustomerById($customerId);
                } else {
                    $this->subscriberFactory->create()->unsubscribeCustomerById($customerId);
                }
                $this->messageManager->addSuccess(__('We updated the subscription.'));
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving your subscription.'));
            }
        }
        $this->_redirect('newsletter/manage/');
    }

}

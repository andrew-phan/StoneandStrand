<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Customer\Override\Controller\Account;

use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditPost extends \Magento\Customer\Controller\AbstractAccount {

    /** @var AccountManagementInterface */
    protected $customerAccountManagement;

    /** @var CustomerRepositoryInterface  */
    protected $customerRepository;

    /** @var Validator */
    protected $formKeyValidator;

    /** @var CustomerExtractor */
    protected $customerExtractor;

    /**
     * @var Session
     */
    protected $session;

    /** @var JsonFactory */
    protected $resultJsonFactory;


    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param Validator $formKeyValidator
     * @param CustomerExtractor $customerExtractor
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator,
        CustomerExtractor $customerExtractor,
        JsonFactory $resultJsonFactory
    ) {
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerExtractor = $customerExtractor;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Change customer password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute() {
        /** @var \Magento\Framework\Controller\Result\JsonFactory $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $messages = array();

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-email",
                "message" => __("You can't leave this empty.")
            );
        }

        if ($this->getRequest()->isPost() && empty($messages)) {
            $typeSubmit = $this->_request->getParam('submit');
            $customerId = $this->session->getCustomerId();
            $currentCustomer = $this->customerRepository->getById($customerId);

            // Prepare new customer data
            $customer = $this->customerExtractor->extract('customer_account_edit', $this->_request);
            $customer->setId($customerId);

            if ($customer->getAddresses() == null) {
                $customer->setAddresses($currentCustomer->getAddresses());
            }
            // Change customer email
            if ($typeSubmit == 'email') {
                $messages = $this->changeCustomerEmail($currentCustomer);
            }

            // Change customer password
            if ($typeSubmit == 'password') {
                $messages = $this->changeCustomerPassword($customerId);
            }

            // Change customer info
            if ($typeSubmit == 'info') {
                $customer->setEmail($currentCustomer->getEmail());
                try {
                    $this->customerRepository->save($customer);
                } catch (AuthenticationException $e) {
                    $messages["status"] = "error";
                    $messages["errors"][] = array(
                        "name" => "input-info",
                        "message" => $e->getMessage()
                    );
                } catch (InputException $e) {
                    $messages["status"] = "error";
                    $messages["errors"][] = array(
                        "name" => "input-info",
                        "message" => __('Invalid input') . $e->getMessage()
                    );
                } catch (\Exception $e) {
                    $messages["status"] = "error";
                    $messages["errors"][] = array(
                        "name" => "input-info",
                        "message" => __("We can't save the customer.") . $e->getMessage()
                    );
                }
            }
        }

        if (empty($messages)) {
            $messages["status"] = "success";
            $messages["message"] = __('Your new information has been changed.');
        }
        
        return $resultJson->setData($messages);
    }

    /**
     * Change customer password
     *
     * @param int $id
     * @return array
     */
    protected function changeCustomerPassword($id) {
        $currPass = $this->getRequest()->getPost('current_password');
        $newPass = $this->getRequest()->getPost('password');
        $confPass = $this->getRequest()->getPost('password_confirmation');
        $messages = array();

        if (!strlen($newPass)) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-password",
                "message" => __('Please enter new password.')
            );
        }

        if ($newPass !== $confPass) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-password",
                "message" => __('Confirm your new password.')
            );
        }

        if (!empty($messages)) {
            return $messages;
        }

        try {
            $this->customerAccountManagement->changePasswordById($id, $currPass, $newPass);
        } catch (AuthenticationException $e) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-password",
                "message" => $e->getMessage()
            );
        } catch (\Exception $e) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-password",
                "message" => __('Something went wrong while changing the password.') . $e->getMessage()
            );
        }

        return $messages;
    }

    /**
     * Change customer email.
     *
     * @param CustomerModel $customer
     * @return array
     * @throws InputException
     * @throws InvalidEmailOrPasswordException
     */
    private function changeCustomerEmail($customer) {
        $currentEmail = $this->getRequest()->getPost('current_email');
        $newEmail = $this->getRequest()->getPost('new_email');
        $confEmail = $this->getRequest()->getPost('email_confirmation');
        $messages = array();

        if (!filter_var($currentEmail, FILTER_VALIDATE_EMAIL)
                || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)
                || !filter_var($confEmail, FILTER_VALIDATE_EMAIL)) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-email",
                "message" => __('Email is not validate.')
            );
        }

        if (!strlen($newEmail)) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-email",
                "message" => __('Please enter new email.')
            );
        }

        if ($newEmail !== $confEmail) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-email",
                "message" => __('Confirm your new email.')
            );
        }

        if (!empty($messages)) {
            return $messages;
        }

        if ($customer->getEmail() != $currentEmail || $currentEmail == $newEmail) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-email",
                "message" =>__('Not like your email.')
            );

            return $messages;
        }

        try {
            $customer->setEmail($newEmail);
            $this->customerRepository->save($customer);
        } catch (AuthenticationException $e) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-email",
                "message" => $e->getMessage()
            );
        } catch (\Exception $e) {
            $messages["status"] = "error";
            $messages["errors"][] = array(
                "name" => "input-email",
                "message" => __('Something went wrong while changing the email.') . $e->getMessage()
            );
        }

        return $messages;
    }

}

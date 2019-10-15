<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile

namespace Ss\Checkout\Model\Override\Api;

/**
 * NVP API wrappers model
 * @TODO: move some parts to abstract, don't hesitate to throw exceptions on api calls
 *
 * @method string getToken()
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Nvp extends \Magento\Paypal\Model\Api\Nvp
{

    /**
     * Handle logical errors
     *
     * @param array $response
     * @return void
     * @throws \Magento\Paypal\Model\Api\ProcessableException|\Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _handleCallErrors($response)
    {
        $errors = $this->_extractErrorsFromResponse($response);
        if (empty($errors)) {
            return;
        }

        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error['message'];
            $this->_callErrors[] = $error['code'];
        }
        $errorMessages = implode(' ', $errorMessages);

        $exceptionLogMessage = sprintf(
            'PayPal NVP gateway errors: %s Correlation ID: %s. Version: %s.', $errorMessages, isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '', isset($response['VERSION']) ? $response['VERSION'] : ''
        );
        $this->_logger->critical($exceptionLogMessage);

        $exceptionPhrase = __('PayPal gateway has rejected request. %1', $errorMessages);

        /** @var \Magento\Framework\Exception\LocalizedException $exception */
        $exception = count($errors) == 1 && $this->_isProcessableError($errors[0]['code']) ? $this->_processableExceptionFactory->create(
                ['phrase' => $exceptionPhrase, 'code' => $errors[0]['code']]
            ) : $this->_frameworkExceptionFactory->create(
                ['phrase' => $exceptionPhrase]
        );

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $session = $objectManager->create('\Magento\Framework\Session\SessionManager');
        $session->setSsError($exceptionPhrase);

        throw $exception;
    }

}

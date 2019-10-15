<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile

namespace Ss\Theme\Block;

/**
 * Block message
 *
 */
class Message extends \Magento\Framework\View\Element\Template
{

    protected $_session;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Session\SessionManager $session
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Session\SessionManager $session, array $data = [])
    {
        $this->_session = $session;
        parent::__construct($context, $data);
    }

    /**
     * @todo To get Message error
     * @return type
     */
    public function getMessageError()
    {
        $message = $this->_session->getSsError();
        if (!empty($message)) {
            $this->_session->setSsError('');
            return $message;
        }
    }

}

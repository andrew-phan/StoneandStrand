<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ss\Newsletter\Block;

use Magento\Framework\View\Element\Template;

/**
 * Newsletter Subscribe block
 */
class Subscribe extends \Magento\Newsletter\Block\Subscribe
{

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);

    }

    /**
     *
     * @param \Magento\Newsletter\Block\Subscribe $originalBlock
     */
    public function beforeToHtml(\Magento\Newsletter\Block\Subscribe $originalBlock)
    {
        $originalBlock->setTemplate('Ss_Newsletter::subscribe.phtml');

    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', ['_secure' => true]);

    }
}

<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Theme\Plugin\Header;

/**
 * Class ProductPlugin
 * Magento\Catalog\Model\Product
 */
class LogoPlugin
{

    protected $_request;

    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context
    )
    {
        $this->_request = $context->getRequest();
    }

    /**
     * @todo To change url Logo 
     * 
     * @param \Magento\Catalog\Model\Product $subject
     * @param \Closure $proceed
     * @return type
     */
    public function aroundgetLogoSrc(\Magento\Theme\Block\Html\Header\Logo $subject, \Closure $proceed)
    {
        $returnValue = $proceed();
        if (!$this->_request->isSecure()) {
            $returnValue = str_replace('https://', 'http://', $returnValue);
        }

        return $returnValue;
    }
}

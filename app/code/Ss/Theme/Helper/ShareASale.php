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

namespace Ss\Theme\Helper;

/**
 * Theme helper
 */
class ShareASale extends \Magento\Framework\App\Helper\AbstractHelper
{
    // ShareASale configuration
    const XML_PATH_ENABLE = 'shareasale/config/enable';
    const XML_PATH_ACCOUNT = 'shareasale/config/merchantid';
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * check active of the ShareASale
     * 
     * @param type $store
     * @return type
     */
    public function isEnable($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    
    /**
     * Get merchant id of the ShareASale
     * 
     * @param type $store
     * @return int
     */
    public function getMerchantId($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_ACCOUNT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
}

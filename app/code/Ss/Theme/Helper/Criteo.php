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

use Mobile_Detect;

/**
 * Theme helper
 */
class Criteo extends \Magento\Framework\App\Helper\AbstractHelper
{
    // Criteo configuration
    const XML_PATH_ENABLE = 'criteo/config/enable';
    const XML_PATH_ACCOUNT = 'criteo/config/account';
    
    const DEVICE_MOBILE = 'm'; // value of mobile device
    const DEVICE_TABLET = 't'; // value of tablet device
    const DEVICE_DESKTOP = 'd'; // value of desktop device

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
     * Get type of device's user
     * 
     * @return string
     */
    public function getDeviceType()
    {
        $deviceType = static::DEVICE_DESKTOP;
        $detect = new Mobile_Detect();
        
        if ($detect->isTablet()) {
            $deviceType = static::DEVICE_TABLET;
        } elseif ($detect->isMobile()) {
            $deviceType = static::DEVICE_MOBILE;
        }
        
        return $deviceType;
    }

    /**
     * check active criteo
     * 
     * @param type $store
     * @return type
     */
    public function isEnable($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    
    /**
     * Get account criteo
     * 
     * @param type $store
     * @return int
     */
    public function getAccount($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_ACCOUNT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
}

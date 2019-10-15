<?php

namespace Ss\Checkout\Plugin;

/**
 * Class CouponManagement
 */
class CouponManagementPlugin
{

    protected $_objectManager;

    public function __construct()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * 
     * @param \Magento\Quote\Model\CouponManagement $subject
     * @param type $cartId
     * @param type $couponCode
     * @return type
     */
    public function aroundSet(
        \Magento\Quote\Model\CouponManagement $subject,
        \Closure $proceed,
        $cartId,
        $couponCode
    )
    {
        $result = $proceed($cartId, $couponCode);
        if ($result === true) {
            $coupon = $this->_objectManager->create('\Magento\SalesRule\Model\Coupon');
            $coupon->loadByCode($couponCode);
            
            $message = $coupon->getMessage() ?: __('Your coupon was successfully applied');

            return json_encode(['message' => $message]);
        }
        
        return $subject;
    }

}

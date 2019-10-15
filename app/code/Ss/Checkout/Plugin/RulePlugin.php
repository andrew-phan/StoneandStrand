<?php

namespace Ss\Checkout\Plugin;

use Magento\SalesRule\Model\Rule;

/**
 * Class CouponManagement
 */
class RulePlugin
{
    /**
     * 
     * @param Rule $subject
     * @param type $result
     */
    public function afterLoadCouponCode(
        Rule $subject,
        $result
    )
    {
        $subject->setCouponMessage($subject->getPrimaryCoupon()->getMessage());
    }

    /**
     * 
     * @param Rule $subject
     * @param type $result
     */
    public function beforeAfterSave(
        Rule $subject
    )
    {
        $couponMessage = trim($subject->getCouponMessage());
        
        if (strlen($couponMessage)
            && $subject->getCouponType() == Rule::COUPON_TYPE_SPECIFIC && !$subject->getUseAutoGeneration()
        ) {
            $subject->getPrimaryCoupon()->setMessage($couponMessage)->save();
        }
    }

}

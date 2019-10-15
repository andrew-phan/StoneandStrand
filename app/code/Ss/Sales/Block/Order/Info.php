<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Sales\Block\Order;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use \Ss\Designer\Helper\Data as designerHelper;

/**
 * Invoice view  comments form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Info extends \Magento\Sales\Block\Order\Info
{
    //Inchoo stripe method
    const INCHOO_STRIPE_METHOD = 'inchoo_stripe';
    
    //Paypal express method
    const PAYPAL_EXPRESS_METHOD = 'paypal_express';
    
    //Prefix order comment default text
    const PREFIX_ORDER_COMMENT_DEFAULT_TEXT = 'ORDER COMMENT:  ';
    

    //Solo, Switch or Maestro CC type code
    const CC_TYPE_SO = 'SO';
    const CC_TYPE_SM = 'SM';
    
    //Visa CC type code
    const CC_TYPE_VI = 'VI';
    
    //Master Card CC type code
    const CC_TYPE_MC = 'MC';
    
    //American Express CC type code
    const CC_TYPE_AE = 'AE';
    
    //Discover CC type code
    const CC_TYPE_DI = 'DI';
    
    //JCB CC type code
    const CC_TYPE_JCB = 'JCB';
    

    /**
     * @var \Ss\Designer\Helper\Data
     */
    protected $_designerHelper;

    /**
     * @param TemplateContext $context
     * @param designerHelper $designerHelper
     * @param Registry $registry
     * @param PaymentHelper $paymentHelper
     * @param AddressRenderer $addressRenderer
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        designerHelper $designerHelper,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
        array $data = []
    ) {
        $this->_designerHelper = $designerHelper;
        parent::__construct($context, $registry, $paymentHelper, $addressRenderer, $data);
    }

    /**
     * Get source image url
     * @return string
     */
    public function getSrcMediaImage()
    {
        return $this->_designerHelper->getSrcMediaImage();

    }

    /**
     * Format total value based on order currency symbol
     *
     * @return  string
     */
    public function currencySymbol()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }
    
    
    /**
     * Check inchoo stripe payment
     *
     * @param   string
     * @return  boolean
     */
    public function isInchooStripePayment($payment_method)
    {
        return ($payment_method == self::INCHOO_STRIPE_METHOD);
    }
    
    /**
     * Check paypal express payment
     *
     * @param   string
     * @return  boolean
     */
    public function isPaypalExpressPayment($payment_method)
    {
        return ($payment_method == self::PAYPAL_EXPRESS_METHOD);
    }
   
    /**
     * Get prefix order comment default text
     *
     * @return  string
     */
    public function getPrefixTextOrderComment()
    {
       return self::PREFIX_ORDER_COMMENT_DEFAULT_TEXT;
    }
}

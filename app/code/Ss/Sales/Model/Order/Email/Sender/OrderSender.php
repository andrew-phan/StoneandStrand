<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class OrderSender
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var OrderResource
     */
    protected $orderResource;

    /**
     * Global configuration storage.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $globalConfig;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * Application Event Dispatcher
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var IdentityInterface
     */
    protected $identityContainer;
    protected $templateContainer;
    protected $config;

    /**
     * @param Template $templateContainer
     * @param OrderIdentity $identityContainer
     * @param Order\Email\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param PaymentHelper $paymentHelper
     * @param OrderResource $orderResource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
     * @param Renderer $addressRenderer
     * @param ManagerInterface $eventManager
     */
    public function __construct(
    Template $templateContainer,
    OrderIdentity $identityContainer,
    \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
    \Psr\Log\LoggerInterface $logger,
    Renderer $addressRenderer,
    PaymentHelper $paymentHelper,
    OrderResource $orderResource,
    \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
    ManagerInterface $eventManager,
    \Magento\Payment\Model\Config $config
    )
    {
        parent::__construct($templateContainer, $identityContainer, $senderBuilderFactory, $logger, $addressRenderer, $paymentHelper, $orderResource, $globalConfig, $eventManager);
        $this->paymentHelper = $paymentHelper;
        $this->orderResource = $orderResource;
        $this->globalConfig = $globalConfig;
        $this->addressRenderer = $addressRenderer;
        $this->eventManager = $eventManager;
        $this->identityContainer = $identityContainer;
        $this->templateContainer = $templateContainer;
        $this->config = $config;
    }

    /**
     * Prepare email template with variables
     *
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order_content = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::ORDER_CONFIRM_CONTENT, 'identifier');
        $order_payment = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::ORDER_CONFIRM_PAYMENT, 'identifier');
        $themeHelper = $objectManager->create("\Ss\Theme\Helper\Data");

        $style = 'style="color: #1d1d1d;font-size: 11px;font-family: OpenSans-Regular, Verdana, sans-serif;margin: 0;letter-spacing: 3px;font-weight: bold;text-transform: uppercase;";';
        $style_2 = 'style="color: #1d1d1d; font-size: 13px; font-family: OpenSans-Regular, Verdana, sans-serif; margin: 0; letter-spacing: 0; font-weight: 400";';
        $style_3 = 'style="border-bottom: 1px solid #b2b2b2";';
        $item = '';
        switch ($order->getPayment()->getMethod()) {
            case \Ss\Sales\Block\Order\Info::INCHOO_STRIPE_METHOD : 
                $ccType = $objectManager->create('Magento\Payment\Model\CcConfig')->getCcAvailableTypes();
                $paymentCreditCard = $ccType[$order->getPayment()->getCcType()];
                $item .= "<tr><td><p " . $style . ">" . __('Method') . "</p></td></tr>";
                  $item .= "<tr><td height='14' " . $style_3 . "></td></tr>";
                  $item .= "<tr><td height='12'></td></tr>";
                  $item .= "<tr><td><p " . $style . ">" . __('CARD TYPE:') . "<span " . $style_2 . ">" . $paymentCreditCard . "</span></p></td></tr>";
                  $item .= "<tr><td height='6'></td></tr>";
                  $item .= "<tr><td><p " . $style . ">" . __('LAST FOUR DIGITS:') . "<span " . $style_2 . ">" . $order->getPayment()->getCcLast4() . "</span></p></td></tr>";
                break;
            case \Ss\Sales\Block\Order\Info::PAYPAL_EXPRESS_METHOD :
                $template = $objectManager->create('Magento\Framework\View\Element\Template');
                $imagePaypal = $template->getViewFileUrl('images/paypal.png');
                $item .= "<tr><td><p " . $style . ">" . __('Method') . "</p></td></tr>";
                $item .= "<tr><td height='14' " . $style_3 . "></td></tr>";
                $item .= "<tr><td height='12'></td></tr>";
                $item .= "<tr><td><p " . $style . "><img src='" . $imagePaypal . "' alt='img-paypal'/></td></tr>";
                break;
            default : {
                    $item .= "<tr><td><p " . $style . ">" . $order->getPayment()->getMethodInstance()->getTitle() . "</p></td></tr>";
                    $item .= "<tr><td height='14' " . $style_3 . "></td></tr>";
                };
        }

        $transport = [
            'order' => $order,
            'item' => $item,
            'order_content' => html_entity_decode(strip_tags($order_content->getContent())),
            'order_payment' => html_entity_decode(strip_tags($order_payment->getContent())),
            'Tribeca_Url' => $themeHelper->getTribecaUrl(),
            'payment_method' => $order->getPayment()->getMethodInstance()->getTitle(),
            'payment_cctype' => $order->getPayment()->getCcType(),
            'payment_cclast4' => $order->getPayment()->getCcLast4(),
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
        ];

        $this->eventManager->dispatch(
            'email_order_set_template_vars_before', ['sender' => $this, 'transport' => $transport]
        );

        $this->templateContainer->setTemplateVars($transport);

        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($order->getCustomerIsGuest()) {
            $templateId = $this->identityContainer->getGuestTemplateId();
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = $this->identityContainer->getTemplateId();
            $customerName = $order->getCustomerName();
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($order->getCustomerEmail());
        $this->templateContainer->setTemplateId($templateId);
    }

}

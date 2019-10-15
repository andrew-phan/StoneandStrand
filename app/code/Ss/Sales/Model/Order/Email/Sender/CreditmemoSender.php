<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order\Creditmemo;

/**
 * Class CreditmemoSender
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreditmemoSender extends \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender
{

    /**
     * Sends order creditmemo email to the customer.
     *
     * Email will be sent immediately in two cases:
     *
     * - if asynchronous email sending is disabled in global settings
     * - if $forceSyncMode parameter is set to TRUE
     *
     * Otherwise, email will be sent later during running of
     * corresponding cron job.
     *
     * @param Creditmemo $creditmemo
     * @param bool $forceSyncMode
     * @return bool
     */
    public function send(Creditmemo $creditmemo, $forceSyncMode = false)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $creditmemo->setSendEmail(true);

        if (!$this->globalConfig->getValue('sales_email/general/async_sending') || $forceSyncMode) {
            $order = $creditmemo->getOrder();
            $themeHelper = $objectManager->create("\Ss\Theme\Helper\Data");
            $state_array = $objectManager->create('Magento\Directory\Model\Country')->setId($themeHelper->getCountryId())->getLoadedRegionCollection()->toOptionArray();
            foreach ($state_array as $_state) {
                if ($_state['value'] == $themeHelper->getRegionId()) {
                    $state = $_state['label'];
                }
            }
            $transport = [
                'order' => $order,
                'creditmemo' => $creditmemo,
                'state' => $state,
                'creditmemo_price' => strip_tags($order->formatPrice($creditmemo->getGrandTotal())),
                'Tribeca_Url' => $themeHelper->getTribecaUrl(),
                'payment_method' => $order->getPayment()->getMethodInstance()->getTitle(),
                'payment_cclast4' => $order->getPayment()->getCcLast4(),
                'Customer_email' => $themeHelper->getEmailSupport(),
                'comment' => $creditmemo->getCustomerNoteNotify() ? $creditmemo->getCustomerNote() : '',
                'billing' => $order->getBillingAddress(),
                'payment_html' => $this->getPaymentHtml($order),
                'store' => $order->getStore(),
                'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
                'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            ];

            $this->eventManager->dispatch(
                'email_creditmemo_set_template_vars_before', ['sender' => $this, 'transport' => $transport]
            );

            $this->templateContainer->setTemplateVars($transport);

            if ($this->checkAndSend($order)) {
                $creditmemo->setEmailSent(true);
                $this->creditmemoResource->saveAttribute($creditmemo, ['send_email', 'email_sent']);
                return true;
            }
        }

        $this->creditmemoResource->saveAttribute($creditmemo, 'send_email');

        return false;
    }

}

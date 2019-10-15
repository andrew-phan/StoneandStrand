<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Rma\Override\Model\Rma\Status;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rma\Model\Rma;
use Magento\Rma\Api\RmaRepositoryInterface;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * RMA model
 * @method \Magento\Rma\Model\Rma\Status\History setStoreId(int $storeId)
 * @method \Magento\Rma\Model\Rma\Status\History setEmailSent(bool $value)
 * @method bool getEmailSent()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class History extends \Magento\Rma\Model\Rma\Status\History
{
    /*     * #@+
     * Data object properties
     */

    const ENTITY_ID = 'entity_id';
    const RMA_ENTITY_ID = 'rma_entity_id';
    const CUSTOMER_NOTIFIED = 'customer_notified';
    const VISIBLE_ON_FRONT = 'visible_on_front';
    const COMMENT = 'comment';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const ADMIN = 'admin';

    /**
     * Core store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Rma factory
     *
     * @var \Magento\Rma\Model\RmaFactory
     */
    protected $_rmaFactory;

    /**
     * Rma configuration
     *
     * @var \Magento\Rma\Model\Config
     */
    protected $_rmaConfig;

    /**
     * Mail transport builder
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Core date model 2.0
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTimeDateTime;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Rma\Helper\Data
     */
    protected $rmaHelper;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var RmaRepositoryInterface
     */
    protected $rmaRepository;

    /**
     * Message manager
     *
     * @var \Magento\Rma\Api\RmaAttributesManagementInterface
     */
    protected $metadataService;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * Sending authorizing email with RMA data
     *
     * @param Rma $rma
     * @param string $rootConfig
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _sendRmaEmailWithItems(Rma $rma, $rootConfig)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $themeHelper = $objectManager->create("\Ss\Theme\Helper\Data");
        $rma_content = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::RMA_CONTENT, 'identifier');

        $storeId = $rma->getStoreId();
        $order = $rma->getOrder();

        $this->_rmaConfig->init($rootConfig, $storeId);
        if (!$this->_rmaConfig->isEnabled()) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $copyTo = $this->_rmaConfig->getCopyTo();
        $copyMethod = $this->_rmaConfig->getCopyMethod();

        if ($order->getCustomerIsGuest()) {
            $template = $this->_rmaConfig->getGuestTemplate();
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = $this->_rmaConfig->getTemplate();
            $customerName = $rma->getCustomerName();
        }

        $sendTo = [['email' => $order->getCustomerEmail(), 'name' => $customerName]];
        if ($rma->getCustomerCustomEmail()) {
            $sendTo[] = ['email' => $rma->getCustomerCustomEmail(), 'name' => $customerName];
        }
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = ['email' => $email, 'name' => null];
            }
        }

        $returnAddress = $this->rmaHelper->getReturnAddress('html', [], $storeId);

        $bcc = [];
        if ($copyTo && $copyMethod == 'bcc') {
            $bcc = $copyTo;
        }

        $state_array = $objectManager->create('Magento\Directory\Model\Country')->setId($themeHelper->getCountryId())->getLoadedRegionCollection()->toOptionArray();
        foreach ($state_array as $_state) {
            if ($_state['value'] == $themeHelper->getRegionId()) {
                $state = $_state['label'];
            }
        }
        foreach ($sendTo as $recipient) {
            $transport = $this->_transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars(
                    [
                        'rma' => $rma,
                        'order' => $order,
                        'state' => $state,
                        'store' => $this->getStore(),
                        'return_address' => $returnAddress,
                        'item_collection' => $rma->getItemsForDisplay(),
                        'formattedShippingAddress' => $this->addressRenderer->format(
                            $order->getShippingAddress(), 'html'
                        ),
                        'formattedBillingAddress' => $this->addressRenderer->format(
                            $order->getBillingAddress(), 'html'
                        ),
                        'Customer_email' => $themeHelper->getEmailSupport(),
                        'Tribeca_Url' => $themeHelper->getTribecaUrl(),
                        'rma_content' => html_entity_decode(strip_tags($rma_content->getContent())),
                    ]
                )
                ->setFrom($this->_rmaConfig->getIdentity())
                ->addTo($recipient['email'], $recipient['name'])
                ->addBcc($bcc)
                ->getTransport();

            $transport->sendMessage();
        }

        $this->setEmailSent(true);

        $this->inlineTranslation->resume();

        return $this;
    }

}

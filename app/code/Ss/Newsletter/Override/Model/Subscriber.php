<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Newsletter\Override\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\MailException;

/**
 * Subscriber model
 *
 * @method \Magento\Newsletter\Model\ResourceModel\Subscriber _getResource()
 * @method \Magento\Newsletter\Model\ResourceModel\Subscriber getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getChangeStatusAt()
 * @method $this setChangeStatusAt(string $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getSubscriberEmail()
 * @method $this setSubscriberEmail(string $value)
 * @method int getSubscriberStatus()
 * @method $this setSubscriberStatus(int $value)
 * @method string getSubscriberConfirmCode()
 * @method $this setSubscriberConfirmCode(string $value)
 * @method int getSubscriberId()
 * @method Subscriber setSubscriberId(int $value)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Subscriber extends \Magento\Newsletter\Model\Subscriber
{
    
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Newsletter\Helper\Data $newsletterData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
//    public function __construct(
//        \Magento\Framework\Model\Context $context,
//        \Magento\Framework\Registry $registry,
//        \Magento\Newsletter\Helper\Data $newsletterData,
//        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
//        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
//        \Magento\Store\Model\StoreManagerInterface $storeManager,
//        \Magento\Customer\Model\Session $customerSession,
//        CustomerRepositoryInterface $customerRepository,
//        AccountManagementInterface $customerAccountManagement,
//        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
//        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
//        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
//        array $data = []
//    ) {
//        $this->_newsletterData = $newsletterData;
//        $this->_scopeConfig = $scopeConfig;
//        $this->_transportBuilder = $transportBuilder;
//        $this->_storeManager = $storeManager;
//        $this->_customerSession = $customerSession;
//        $this->customerRepository = $customerRepository;
//        $this->customerAccountManagement = $customerAccountManagement;
//        $this->inlineTranslation = $inlineTranslation;
//        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
//    }

    
    /**
     * Sends out confirmation success email
     *
     * @return $this
     */
    public function sendConfirmationSuccessEmail()
    {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $themeHelper = $objectManager->create("\Ss\Theme\Helper\Data");
        $welcome_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SIGNUP_WELCOME_STONE_STRAND, 'identifier');
        $lifetime_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SIGNUP_LIFETIME_WARRANTY, 'identifier');
        $shipping_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SIGNUP_FREE_SHIPPING, 'identifier');
        $cash_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SIGNUP_SS_CASH, 'identifier');
        $media = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        
        if ($this->getImportMode()) {
            return $this;
        }

        if (!$this->_scopeConfig->getValue(
            self::XML_PATH_SUCCESS_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) || !$this->_scopeConfig->getValue(
            self::XML_PATH_SUCCESS_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier(
            $this->_scopeConfig->getValue(
                self::XML_PATH_SUCCESS_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->setTemplateOptions(
            [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            [
                'subscriber' => $this,
                'signup_banner' => $themeHelper->getSignUpBanner(),
                'Tribeca_Url' => $themeHelper->getTribecaUrl(),
                'telephone' => $themeHelper->getTelephone(),
                'welcome_title' => $welcome_post->getTitle(),
                'welcome_content' => html_entity_decode(strip_tags($welcome_post->getContent())),
                'lifetime_title' => $lifetime_post->getTitle(),
                'lifetime_content' => html_entity_decode(strip_tags($lifetime_post->getContent())),
                'lifetime_image' => $media . $lifetime_post->getFeaturedImg(),
                'shipping_title' => $shipping_post->getTitle(),
                'shipping_content' => html_entity_decode(strip_tags($shipping_post->getContent())),
                'shipping_image' => $media . $shipping_post->getFeaturedImg(),
                'cash_title' => $cash_post->getTitle(),
                'cash_content' => html_entity_decode(strip_tags($cash_post->getContent())),
                'cash_image' => $media . $cash_post->getFeaturedImg(),
            ]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_SUCCESS_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->addTo(
            $this->getEmail(),
            $this->getName()
        );
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }

}

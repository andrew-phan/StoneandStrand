<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Customer\Override\Model;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\ValidationResultsInterfaceFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;


/**
 * Handle various customer account actions
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class AccountManagement extends \Magento\Customer\Model\AccountManagement
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @param CustomerFactory $customerFactory
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param Random $mathRandom
     * @param Validator $validator
     * @param ValidationResultsInterfaceFactory $validationResultsDataFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomerMetadataInterface $customerMetadataService
     * @param CustomerRegistry $customerRegistry
     * @param PsrLogger $logger
     * @param Encryptor $encryptor
     * @param ConfigShare $configShare
     * @param StringHelper $stringHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param DataObjectProcessor $dataProcessor
     * @param Registry $registry
     * @param CustomerViewHelper $customerViewHelper
     * @param DateTime $dateTime
     * @param CustomerModel $customerModel
     * @param ObjectFactory $objectFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CustomerFactory $customerFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Random $mathRandom,
        Validator $validator,
        ValidationResultsInterfaceFactory $validationResultsDataFactory,
        AddressRepositoryInterface $addressRepository,
        CustomerMetadataInterface $customerMetadataService,
        CustomerRegistry $customerRegistry,
        PsrLogger $logger,
        Encryptor $encryptor,
        ConfigShare $configShare,
        StringHelper $stringHelper,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        DataObjectProcessor $dataProcessor,
        Registry $registry,
        CustomerViewHelper $customerViewHelper,
        DateTime $dateTime,
        CustomerModel $customerModel,
        ObjectFactory $objectFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($customerFactory, $eventManager, $storeManager, $mathRandom, $validator, $validationResultsDataFactory, $addressRepository, $customerMetadataService, $customerRegistry, $logger, $encryptor, $configShare, $stringHelper, $customerRepository, $scopeConfig, $transportBuilder, $dataProcessor, $registry, $customerViewHelper, $dateTime, $customerModel, $objectFactory, $extensibleDataObjectConverter);
    }

    /**
     * Send email with new account related information
     *
     * @param CustomerInterface $customer
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @param string $sendemailStoreId
     * @return $this
     * @throws LocalizedException
     */
    public function SendNewAccountEmail(
    $customer, $type = self::NEW_ACCOUNT_EMAIL_REGISTERED, $backUrl = '', $storeId = '0', $sendemailStoreId = null
    )
    {
        if ($this->scopeConfig->getValue(
            'customer/create_account/disable_welome_email_customer',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return;
        }
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $types = parent::getTemplateTypes();
        if (!isset($types[$type])) {
            throw new LocalizedException(__('Please correct the transactional account email type.'));
        }
        if (!$storeId) {
            $storeId = parent::getWebsiteStoreId($customer, $sendemailStoreId);
        }
        $store = $this->storeManager->getStore($customer->getStoreId());
        $customerEmailData = parent::getFullCustomerObject($customer);
        $media = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $welcome_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SIGNUP_WELCOME_STONE_STRAND, 'identifier');
        $lifetime_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SIGNUP_LIFETIME_WARRANTY, 'identifier');
        $shipping_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SIGNUP_FREE_SHIPPING, 'identifier');
        $cash_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SIGNUP_SS_CASH, 'identifier');

        $themeHelper = $objectManager->create("\Ss\Theme\Helper\Data");
        parent::sendEmailTemplate(
            $customer, $types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY, [
            'customer' => $customerEmailData,
            'back_url' => $backUrl,
            'store' => $store,
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
            ], $storeId
        );

        return $this;
    }


}
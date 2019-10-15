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
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    protected $_isCustomerVip;

    const ROOT_CATEGORY_ID = 1; // Root category
    const DEPTH_CATEGORY_RENDER = 4; // Depth render category
    const LIMIT_PRODUCT_RELATED = 8; // Limit related product will show on product page
    const URL_DESIGNER_LISTING_PAGE = 'designers'; // url designer listing page
    const URL_JEWELRY_PAGE = 'jewelry'; // url jewelry page
    const URL_JEWELRY_VINTAGE_PAGE = 'jewelry/vintage'; // url jewelry vintage page
    const URL_POPUP_DESIGNER_PAGE = 'designers/epiphanie-jewelry-jewelry'; // url designer listing page
    const URL_GIVEBACK_DESIGNER_PAGE = 'jewelry/bracelets/keep-a-child-alive-committed-to-the-end-bracelet'; // url designer listing page
    const URL_CONTACT_US_PAGE = 'contact-us'; // url contact us page
    const PREFIX_SHIPPING_DATE = "";  // Prefix shipping date field
    const SUFFIX_SHIPPING_DATE = "m/d/Y";     // Format shipping date
    const IS_FILTER_DESIGNER_PAGE = 'is_filter_designer';   // Check is filter on designer page
    const URL_DIAMONDS_PAGE = "ss-diamonds";    // Url diamonds page
    const PREFIX_URL_REWRITE_DIAMONDS = "ss-diamonds-";    // Url diamonds page
    const PARAM_COLLAPSE = "collapse";    // Param collapse
    const LIMIT_FULL_NAME = 8; // Limit character for full name customer
    const PRODUCT_PAGE_NO_IMAGE = 'no_selection';

    // Social configuration
    const XML_PATH_INSTAGRAM = 'sstheme/sstheme_page/social_instagram';
    const XML_PATH_FACEBOOK = 'sstheme/sstheme_page/social_facebook';
    const XML_PATH_TWITTER = 'sstheme/sstheme_page/social_twitter';
    const XML_PATH_PIN = 'sstheme/sstheme_page/social_pin';

    // Contact configuration
    const XML_PATH_STORE_NAME = 'general/store_information/name';
    const XML_PATH_TELEPHONE = 'general/store_information/phone';
    const XML_PATH_COUNTRY = 'general/store_information/country_id';
    const XML_PATH_REGION = 'general/store_information/region_id';
    const XML_PATH_CUSTOMER_VIP = 'sstheme/sstheme_customer_vip/customer_group';
    const XML_PATH_TRIBECA_URL = 'sstheme/sstheme_contact/tribeca_url';

    //
    const XML_PATH_CUSTOMER_LOGO = 'sstheme/sstheme_customer_vip/customer_logo';
    const XML_PATH_CATALOG_MAX_QTY = 'sstheme/sstheme_catalog_product/catalog_max_qty';
    const XML_PATH_PLACEHOLDER_IMAGE = 'sstheme/sstheme_header/placeholder_image';

    // Email address configuration
    const XML_ATTRIBUTE_SET_DIAMOND = 'sstheme/sstheme_diamond/attribute_set_diamond';
    const XML_ATTRIBUTE_SET_NORMAL = 'sstheme/sstheme_diamond/attribute_set_normal';
    const XML_PATH_EMAIL_CAREERS = 'sstheme/email_config/careers_email';
    const XML_PATH_EMAIL_CUSTOMERS = 'sstheme/email_config/customer_email';
    const XML_PATH_EMAIL_DESIGNERS = 'sstheme/email_config/designers_email';
    const XML_PATH_NAME_GENERAL = 'sstheme/email_config/general_name';
    const XML_PATH_EMAIL_GENERAL = 'sstheme/email_config/general_email';
    const XML_PATH_EMAIL_PRESS = 'sstheme/email_config/press_email';
    const XML_PATH_EMAIL_TRIBECA = 'sstheme/email_config/tribeca_email';

    // Designer configuration
    const XML_PATH_DESIGNER_CATEGORY = 'sstheme/sstheme_designer/root_category';
    const XML_PATH_DESIGNER_PAGE_TITLE = 'sstheme/sstheme_designer/page_title';
    const XML_PATH_DESIGNER_KEYWORD = 'sstheme/sstheme_designer/keyword';
    const XML_PATH_DESIGNER_DESCRIPTION = 'sstheme/sstheme_designer/description';

    // Email Header template configuration
    const XML_PATH_MENU_1_TITLE = 'email/email_header/menu_1_title';
    const XML_PATH_MENU_1_LINK = 'email/email_header/menu_1_link';
    const XML_PATH_MENU_2_TITLE = 'email/email_header/menu_2_title';
    const XML_PATH_MENU_2_LINK = 'email/email_header/menu_2_link';
    const XML_PATH_MENU_3_TITLE = 'email/email_header/menu_3_title';
    const XML_PATH_MENU_3_LINK = 'email/email_header/menu_3_link';
    const XML_PATH_MENU_4_TITLE = 'email/email_header/menu_4_title';
    const XML_PATH_MENU_4_LINK = 'email/email_header/menu_4_link';
    const XML_PATH_MENU_5_TITLE = 'email/email_header/menu_5_title';
    const XML_PATH_MENU_5_LINK = 'email/email_header/menu_5_link';
    const XML_PATH_TOP_MESSAGE_LINK = 'email/email_header/top_message_link';
    const XML_PATH_TOP_MESSAGE = 'email/email_header/top_message';
    const XML_PATH_SIGN_UP_BANNER = 'email/email_header/sign_up_banner';
    const XML_PATH_ACCOUNT_SHIPPING_URL = 'sstheme/account_config/account_shipping_url';
    const XML_PATH_ACCOUNT_POLICY_URL = 'sstheme/account_config/account_policy_url';
    const XML_PATH_ACCOUNT_EXCHANGE_URL = 'sstheme/account_config/account_exchange_url';
    
    // Tax configuration
    const XML_PATH_INCLUDE_TAX_IN_ORDER_TOTAL = 'tax/sales_display/grandtotal';

    protected $_storeManager;
    protected $_request;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Action\Context $contextRequest
    )
    {
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_storeManager = $storeManager;
        $this->_request = $contextRequest->getRequest();
        parent::__construct($context);
    }

    /**
     * @todo To get setting instagram.
     * @param type $store
     * @return type
     */
    public function getUrlSocialInstagram($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_INSTAGRAM, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting facebook
     * @param type $store
     * @return type
     */
    public function getUrlSocialFacebook($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_FACEBOOK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting twitter
     * @param type $store
     * @return type
     */
    public function getUrlSocialTwitter($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_TWITTER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting pin
     * @param type $store
     * @return type
     */
    public function getUrlSocialPin($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_PIN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting telephone
     * @param type $store
     * @return type
     */
    public function getTelephone($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_TELEPHONE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    /**
     * @todo To get setting country
     * @param type $store
     * @return type
     */
    public function getCountryId($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_COUNTRY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting region
     * @param type $store
     * @return type
     */
    public function getRegionId($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_REGION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }


    /**
     * @todo To get setting customer group
     * @param type $store
     * @return type
     */
    public function getCustomerGroupVip($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_CUSTOMER_VIP, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    
     /**
     * @todo To get config include tax order total
     * @param type $store
     * @return type
     */
    public function getIsIncludeTaxInOrderTotal($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_INCLUDE_TAX_IN_ORDER_TOTAL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting max qty
     * @param type $store
     * @return type
     */
    public function getMaxQtyProduct($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_CATALOG_MAX_QTY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo to get url image from theme setting.
     * @param type $url
     * @return string
     */
    public function getUrlImageFromConfig($fileName)
    {
        $urlImage = $this->_urlBuilder
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA, '_secure' => $this->_request->isSecure()]);

        if (!empty($fileName)) {
            $folderName = \Magento\Config\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
            $path = $folderName . '/' . $fileName;
            $urlImage .= $path;
        }
        return $urlImage;
    }

    /**
     * @todo to get logo for customer group VIP
     * @param type $store
     * @return type
     */
    public function getCustomerGroupVipLogo($store = null)
    {
        $urlLogo = $this->scopeConfig->getValue(static::XML_PATH_CUSTOMER_LOGO, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $this->getUrlImageFromConfig($urlLogo);
    }

    /**
     * @todo To check if current user is belong to group VIP.
     * @return type
     */
    public function checkIsCustomerGroupVip()
    {

        if (is_null($this->_isCustomerVip)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->create('Magento\Customer\Model\Session');
            $this->_isCustomerVip = false;

            if ($customerSession->isLoggedIn()) {
                $customerGroupId = $customerSession->getCustomerGroupId();

                if ($customerGroupId == $this->getCustomerGroupVip()) {
                    $this->_isCustomerVip = true;
                }
            }
        }

        return $this->_isCustomerVip;
    }

    /**
     * @todo To get placeholder image for post
     * @param type $store
     * @return type
     */
    public function getPlaceHolderImage($store = null)
    {
        $urlLogo = $this->scopeConfig->getValue(static::XML_PATH_PLACEHOLDER_IMAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $this->getUrlImageFromConfig($urlLogo);
    }

    /**
     * @todo To get setting S&S Tribeca link in contact form
     * @param type $store
     * @return type
     */
    public function getTribecaUrl($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_TRIBECA_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting max qty
     * @param type $store
     * @return type
     */
    public function getEmailToLink($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_EMAIL_TRIBECA, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting max qty
     * @param type $store
     * @return type
     */
    public function getEmailSupport($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_EMAIL_CUSTOMERS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }



    /**
     * @todo To get store name
     * @param type $store
     * @return type
     */
    public function getStoreName($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_STORE_NAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    
    /**
     * @todo To get name general
     * @param type $store
     * @return type
     */
    public function getNameGeneral($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_NAME_GENERAL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get email general
     * @param type $store
     * @return type
     */
    public function getEmailGeneral($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_EMAIL_GENERAL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

        /**
     * @todo To get email Designers
     * @param type $store
     * @return type
     */
    public function getEmailDesigners($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_EMAIL_DESIGNERS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }


    /**
     * @todo to get config designer root category
     * @param type $store
     * @return type
     */
    public function getConfigDesignerCategory($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_DESIGNER_CATEGORY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo to get attribute set diamond
     * @param type $store
     * @return type
     */
    public function getConfigAttributeSetDiamond($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_ATTRIBUTE_SET_DIAMOND, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo to get attribute set normal
     * @param type $store
     * @return type
     */
    public function getConfigAttributeSetNormal($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_ATTRIBUTE_SET_NORMAL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo to get current url
     * @param type $store
     * @return type
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * Get System Config
     * @param string $configKey
     * @param string $store
     * @return string
     */
    public function getSystemConfig($configKey, $store = null)
    {
        if (!$configKey) {
            return '';
        }
        return $this->scopeConfig->getValue($configKey, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 1 title
     * @param type $store
     * @return type
     */
    public function getTitleMenu1($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_1_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 1 link
     * @param type $store
     * @return type
     */
    public function getLinkMenu1($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_1_LINK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 2 title
     * @param type $store
     * @return type
     */
    public function getTitleMenu2($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_2_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 2 link
     * @param type $store
     * @return type
     */
    public function getLinkMenu2($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_2_LINK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 3 title
     * @param type $store
     * @return type
     */
    public function getTitleMenu3($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_3_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 3 link
     * @param type $store
     * @return type
     */
    public function getLinkMenu3($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_3_LINK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 4 title
     * @param type $store
     * @return type
     */
    public function getTitleMenu4($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_4_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 4 link
     * @param type $store
     * @return type
     */
    public function getLinkMenu4($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_4_LINK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 5 title
     * @param type $store
     * @return type
     */
    public function getTitleMenu5($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_5_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting menu 5 link
     * @param type $store
     * @return type
     */
    public function getLinkMenu5($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_MENU_5_LINK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting top message link
     * @param type $store
     * @return type
     */
    public function getLinkTopMessage($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_TOP_MESSAGE_LINK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting top message
     * @param type $store
     * @return type
     */
    public function getTopMessage($store = null)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_TOP_MESSAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @todo To get setting sign up banner
     * @param type $store
     * @return type
     */
    public function getSignUpBanner($store = null)
    {
        $urlLogo = $this->scopeConfig->getValue(static::XML_PATH_SIGN_UP_BANNER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $this->getUrlImageFromConfig($urlLogo);
    }


}

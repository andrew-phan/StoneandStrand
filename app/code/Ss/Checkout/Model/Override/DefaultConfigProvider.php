<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Checkout\Model\Override;

use Magento\Checkout\Helper\Data as CheckoutHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Url as CustomerUrlManager;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Data\Form\FormKey;
use Magento\Quote\Api\CartItemRepositoryInterface as QuoteItemRepository;
use Magento\Quote\Api\ShippingMethodManagementInterface as ShippingMethodManager;
use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Default item reder in checkout page
 */
class DefaultConfigProvider extends \Magento\Checkout\Model\DefaultConfigProvider
{

    protected $_designerFactory;
    protected $_checkoutSession;
    protected $_productFactory;
    protected $_listDesigners;
    protected $_productHelper;
    protected $_lastestShippingDate;

    /**
     *
     * @param CheckoutHelper $checkoutHelper
     * @param CheckoutSession $checkoutSession
     * @param CustomerRepository $customerRepository
     * @param CustomerSession $customerSession
     * @param CustomerUrlManager $customerUrlManager
     * @param HttpContext $httpContext
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param QuoteItemRepository $quoteItemRepository
     * @param ShippingMethodManager $shippingMethodManager
     * @param ConfigurationPool $configurationPool
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param LocaleFormat $localeFormat
     * @param \Magento\Customer\Model\Address\Mapper $addressMapper
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param FormKey $formKey
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param \Magento\Directory\Model\Country\Postcode\ConfigInterface $postCodesConfig
     * @param \Magento\Checkout\Model\Cart\ImageProvider $imageProvider
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param CartTotalRepositoryInterface $cartTotalRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingMethodConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param UrlInterface $urlBuilder
     * @param \Ss\Designer\Model\DesignerFactory $designerFactory
     */
    public function __construct(
    CheckoutHelper $checkoutHelper,
        CheckoutSession $checkoutSession,
        CustomerRepository $customerRepository,
        CustomerSession $customerSession,
        CustomerUrlManager $customerUrlManager,
        HttpContext $httpContext,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        QuoteItemRepository $quoteItemRepository,
        ShippingMethodManager $shippingMethodManager,
        ConfigurationPool $configurationPool,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        LocaleFormat $localeFormat,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Customer\Model\Address\Config $addressConfig,
        FormKey $formKey,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Directory\Model\Country\Postcode\ConfigInterface $postCodesConfig,
        \Magento\Checkout\Model\Cart\ImageProvider $imageProvider,
        \Magento\Directory\Helper\Data $directoryHelper,
        CartTotalRepositoryInterface $cartTotalRepository,
        ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingMethodConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        UrlInterface $urlBuilder,
        \Ss\Designer\Model\DesignerFactory $designerFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Plumrocket\Estimateddelivery\Helper\Product $productHelper
    )
    {
        parent::__construct($checkoutHelper, $checkoutSession, $customerRepository, $customerSession, $customerUrlManager, $httpContext, $quoteRepository, $quoteItemRepository, $shippingMethodManager, $configurationPool, $quoteIdMaskFactory, $localeFormat, $addressMapper, $addressConfig, $formKey, $imageHelper, $viewConfig, $postCodesConfig, $imageProvider, $directoryHelper, $cartTotalRepository, $scopeConfig, $shippingMethodConfig, $storeManager, $paymentMethodManagement, $urlBuilder);
        $this->_designerFactory = $designerFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_productFactory = $productFactory;
        $this->_productHelper = $productHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $results = parent::getConfig();
        $results["totalsData"]["items"] = $this->getTotalsDataProduct();
        
        if ( ! empty($this->_lastestShippingDate)) {
            $results["ssShippingDate"] = \Ss\Theme\Helper\Data::PREFIX_SHIPPING_DATE . date(\Ss\Theme\Helper\Data::SUFFIX_SHIPPING_DATE, $this->_lastestShippingDate);
        }
        
        return $results;
    }

    /**
     * Return quote totals data
     * @return array
     */
    private function getTotalsDataProduct()
    {
        $quote = $this->_checkoutSession->getQuote();

        /** @var \Magento\Quote\Api\Data\TotalsInterface $totals */
        $totals = $this->cartTotalRepository->get($quote->getId());

        $items = [];
        /** @var  \Magento\Quote\Model\Cart\Totals\Item $item */
        foreach ($totals->getItems() as $item) {
            $productId = $quote->getItemById($item->getItemId())->getProductId();
            $product = $this->_productFactory->create()->load($productId);
            $designerId = $product->getSsDesigner();
            $data = $item->__toArray();
            $data["product_id"] = $productId;

            if ($designerId) {
                if (!$this->_listDesigners || !isset($this->_listDesigners[$designerId])) {
                    $designer = $this->_designerFactory->create()->load($designerId);
                    $this->_listDesigners[$designerId] = ($designer->getIsActive()) ? $designer : '';
                }
            } else {
                $designerId = \Ss\Designer\Model\Designer::KEY_NO_DESIGNER;
                $this->_listDesigners[$designerId] = '';
            }

            $data["designer_name"] = (empty($this->_listDesigners[$designerId])) ? '' : $this->_listDesigners[$designerId]->getName();
            $data["designer_url"] = (empty($this->_listDesigners[$designerId])) ? '' : $this->_listDesigners[$designerId]->getUrl();
            $data["extension_attributes"]["designer_name"] = (empty($this->_listDesigners[$designerId])) ? '' : $this->_listDesigners[$designerId]->getName();
            $data["extension_attributes"]["designer_url"] = (empty($this->_listDesigners[$designerId])) ? '' : $this->_listDesigners[$designerId]->getUrl();
            $items[] = $data;

            // Check and get lastest shipping date.
            $this->_productHelper->setProduct($product);
            $this->checkShippingDate();
        }

        return $items;
    }

    /**
     * @todo To check shipping date from product.
     * @return type
     */
    public function checkShippingDate()
    {
        if ($this->_productHelper->hasShippingDate()) {
            if ($this->_productHelper->formatShippingDate() == 'date') {
                $value = $this->_productHelper->getShippingFromTime();
                if ($this->_productHelper->getShippingToTime() && ($this->_productHelper->getShippingToTime() != $this->_productHelper->getShippingFromTime())) {
                    $value = $this->_productHelper->getShippingToTime();
                }
            } else {
                $value = $this->_productHelper->getShippingTime();
            }

            if ($value > $this->_lastestShippingDate) {
                $this->_lastestShippingDate = $value;
            }
        }
    }

}

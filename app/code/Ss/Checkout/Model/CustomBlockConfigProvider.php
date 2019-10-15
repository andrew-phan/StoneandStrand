<?php

namespace Ss\Checkout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class add config to js
 */
class CustomBlockConfigProvider implements ConfigProviderInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfiguration;
    protected $_attributeHelper;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration,
        \Ss\Theme\Helper\Attributes $attributeHelper
    )
    {
        $this->scopeConfiguration = $scopeConfiguration;
        $this->_attributeHelper = $attributeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $showHide['ss_reason_buy'] = $this->_attributeHelper->getOptionsCustomerHowYouHear();
        return $showHide;
    }

}

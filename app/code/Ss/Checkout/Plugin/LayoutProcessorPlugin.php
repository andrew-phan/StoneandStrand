<?php

namespace Ss\Checkout\Plugin;

/**
 * Class LayoutProcessorPlugin
 */
class LayoutProcessorPlugin
{

    protected $_themeHelper;

    public function __construct(
    \Ss\Theme\Helper\Data $themeHelper
    )
    {
        $this->_themeHelper = $themeHelper;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
    \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    )
    {
        $idNewYorkRegion = $this->_themeHelper->getRegionId();

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']["region_id"]["label"] = __("State / Province");

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']["region_id"]["value"] = $idNewYorkRegion;

        // Change position and validate form billing address.
        $paymentChildren = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children'];

        foreach ($paymentChildren as $key => $item) {
            if (isset($item["children"]["form-fields"])) {
                $formFields = $item["children"]["form-fields"]["children"];
                $formFields["region_id"]["sortOrder"] = 100;
                $formFields["region_id"]["value"] = $idNewYorkRegion;
                $formFields["region_id"]["label"] = __("State / Province");
                $formFields["telephone"]["validation"]["validate-number"] = true;
                $formFields["telephone"]["validation"]["max_text_length"] = 14;
                $formFields["country_id"]["sortOrder"] = 90;

                $paymentChildren[$key]["children"]["form-fields"]["children"] = $formFields;
            }
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children'] = $paymentChildren;

        return $jsLayout;
    }

}

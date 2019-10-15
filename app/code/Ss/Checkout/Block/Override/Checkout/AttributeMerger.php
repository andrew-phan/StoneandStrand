<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Checkout\Block\Override\Checkout;

/**
 * Class checkout AttributeMerger
 */
class AttributeMerger extends \Magento\Checkout\Block\Checkout\AttributeMerger
{

    /**
     * Retrieve field configuration for street address attribute
     *
     * @param string $attributeCode
     * @param array $attributeConfig
     * @param string $providerName name of the storage container used by UI component
     * @param string $dataScopePrefix
     * @return array
     */

    protected function getMultilineFieldConfig($attributeCode,
        array $attributeConfig,
        $providerName,
        $dataScopePrefix)
    {
        $results = parent::getMultilineFieldConfig($attributeCode, $attributeConfig, $providerName, $dataScopePrefix);
        if ($attributeCode == 'street') {
            $count = 1;
            foreach ($results['children'] as $key => $item) {
                $item["config"]["elementTmpl"] = "Ss_Checkout/form/element/input";
                $item['label'] = ($count == 2) ? __('Apt. Suite. Etc.') : $attributeConfig['label'];
                $results['children'][$key] = $item;
                $count++;
            }
        }

        return $results;
    }

    /**
     * Order country options. Move top countries to the beginning of the list.
     *
     * @param array $countryOptions
     * @return array
     */
    protected function orderCountryOptions(array $countryOptions)
    {
        if (empty($this->topCountryCodes)) {
            $countryOptions[0]['label'] = __('Please select the country.');

            return $countryOptions;
        }

        $headOptions = [];
        $tailOptions = [[
            'value' => 'delimiter',
            'label' => '──────────',
            'disabled' => true,
        ]];
        foreach ($countryOptions as $countryOption) {
            if (empty($countryOption['value']) || in_array($countryOption['value'], $this->topCountryCodes)) {
                array_push($headOptions, $countryOption);
            } else {
                array_push($tailOptions, $countryOption);
            }
        }
        return array_merge($headOptions, $tailOptions);
    }

}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Ss\Designer\Helper\Override\ShopbySeo;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Manager;

/**
 * Helper Url
 */
class Url extends \Amasty\ShopbySeo\Helper\Url
{
    
    /**
     * @todo tranfer data query to aliases
     * @param array $query
     * @return type
     */
    protected function query2Aliases(array &$query)
    {
        $optionsData = $this->helper->getOptionsSeoData();

        $seoAliases = [];
        foreach ($query as $key => $queryArgument) {
            $argumentParts = explode('=', $queryArgument, 2);
            $paramName = $argumentParts[0];
            if (count($argumentParts) == 2 && $this->isParamSeoSignificant($paramName)) {
                $values = explode(',', str_replace('%2C', ',', $argumentParts[1]));
                foreach ($values as $value) {
                    if($paramName == \Ss\Designer\Model\Designer::ATTRIBUTE_CODE){
                        $value = \Ss\Designer\Model\Designer::ATTRIBUTE_CODE . $value;
                    }

                    if (!array_key_exists($value, $optionsData)) {
                        continue;
                    }
                    $alias = $optionsData[$value]['alias'];
                    unset($query[$key]);
                    $seoAliases[] = $alias;
                }
            }
        }

        return $seoAliases;
    }
}
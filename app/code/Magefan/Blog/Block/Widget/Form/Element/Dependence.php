<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Form element dependencies mapper
 * Assumes that one element may depend on other element values.
 * Will toggle as "enabled" only if all elements it depends from toggle as true.
 */
namespace Magefan\Blog\Block\Widget\Form\Element;

class Dependence extends \Magento\Backend\Block\Widget\Form\Element\Dependence
{    
    /**
     * Register field name dependence one from each other by specified values
     *
     * @param string $fieldName
     * @param string $fieldNameFrom
     * @param \Magento\Config\Model\Config\Structure\Element\Dependency\Field|string $refField
     * @return \Magento\Backend\Block\Widget\Form\Element\Dependence
     */
    public function addFieldDependence($fieldName, $fieldNameFrom, $refField)
    {        
        if (!is_object($refField)) {
            
            /** @var $refField \Magento\Config\Model\Config\Structure\Element\Dependency\Field */
            $refField = $this->_fieldFactory->create(
                ['fieldData' => ['value' => (string)$refField , 'separator' => ','], 'fieldPrefix' => '']
            );
        }
        
        $this->_depends[$fieldName][$fieldNameFrom] = $refField;
        return $this;
    }
    
}

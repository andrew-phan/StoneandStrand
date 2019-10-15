<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Customer\Block\Address;

use \Magento\Framework\Profiler;

/**
 * Customer address edit block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Edit extends \Magento\Customer\Block\Address\Edit
{
    /**
     * @return \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    public function getGenderCollection()
    {
        $collection = $this->getData('region_collection');
        if ($collection === null) {
            $collection = $this->_regionCollectionFactory->create()->addCountryFilter($this->getCountryId())->load();

            $this->setData('region_collection', $collection);
        }
        return $collection;
    }

    /**
     * @return string
     */
    public function getGenderHtmlSelect()
    {
        Profiler::start('TEST: ' . __METHOD__, ['group' => 'TEST', 'method' => __METHOD__]);
        
        $cacheKey = 'GENDER_SELECT_STORE' . $this->_storeManager->getStore()->getId();
        $cache = $this->_configCacheType->load($cacheKey);
        
        if ($cache) {
            $options = unserialize($cache);
        } else {
            $options = $this->getGenderCollection()->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        
        $html = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            'gender'
        )->setTitle(
            __('Gender')
        )->setId(
            'gender'
        )->setClass(
            'required-entry validate-state'
        )->setValue(
            intval($this->getData('gender'))
        )->setOptions(
            $options
        )->getHtml();
        
        Profiler::start('TEST: ' . __METHOD__, ['group' => 'TEST', 'method' => __METHOD__]);
        
        return $html;
    }

    /**
     * @param null|string $defValue
     * @param string $name
     * @param string $id
     * @param string $title
     * @return string
     */
    public function getCountryHtmlSelect($defValue = null, $name = 'country_id', $id = 'country', $title = 'Country')
    {
        Profiler::start('TEST: ' . __METHOD__, ['group' => 'TEST', 'method' => __METHOD__]);
        
        if ($defValue === null) {
            $defValue = $this->getCountryId();
        }
        
        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_' . $this->_storeManager->getStore()->getCode();
        $cache = $this->_configCacheType->load($cacheKey);
        if ($cache) {
            $options = unserialize($cache);
        } else {
            $options = $this->getCountryCollection()
                ->setForegroundCountries($this->getTopDestinations())
                ->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        
        unset($options[0]);
        
        $html = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            $name
        )->setId(
            $id
        )->setTitle(
            __($title)
        )->setValue(
            $defValue
        )->setOptions(
            $options
        )->setExtraParams(
            ' required="" data-parsley-required-message="<span class=\'img-icon icon-error\' data-toggle-error>&nbsp;</span><p class=\'hidden\'>You can\'t leave this empty.</p>" data-change-target-required'
        )->getHtml();
        
        Profiler::stop('TEST: ' . __METHOD__);
        
        return $html;
    }
    
    public function getRegionId() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $themeHelper = $objectManager->create("\Ss\Theme\Helper\Data");
        
        $region = $this->getAddress()->getRegion();
        return $region === null ? $themeHelper->getRegionId() : $region->getRegionId();
    }
}

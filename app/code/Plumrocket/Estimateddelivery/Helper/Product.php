<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Estimateddelivery
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Estimateddelivery\Helper;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_sourceData;
	protected $_helper;
	protected $_productCategoryModel;
    protected $_objectManager;
	protected $_localeResolver;
	protected $_localeDate;

	public function __construct(
		\Plumrocket\Estimateddelivery\Helper\Data $helper,
		\Plumrocket\Estimateddelivery\Model\ProductCategory $productCategoryModel,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Locale\ResolverInterface $localeResolver,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
		\Magento\Framework\App\Helper\Context $context
	) {
        parent::__construct($context);

        $this->_helper = $helper;
        $this->_productCategoryModel = $productCategoryModel;
        $this->_objectManager = $objectManager;
        $this->_localeResolver = $localeResolver;
        $this->_localeDate = $localeDate;
    }

	public function isEnabled()
	{
		return $this->_helper->moduleEnabled();
	}

	public function setCategory($category)
	{
		$this->_productCategoryModel->setCategory($category);
		return $this;
	}

	public function setProduct($product)
	{
		$this->reset();
		$this->_productCategoryModel->setProduct($product);
		return $this;
	}

	public function reset()
	{
		$this->_productCategoryModel->reset();
		$this->_sourceData = null;
		return $this;
	}

	public function getProduct()
	{
		return $this->_productCategoryModel->getProduct();
	}

	public function getCategory()
	{
		return $this->_productCategoryModel->getCategory();
	}

	// protected  ---------

	protected function _param($type, $param, $default = false)
	{
		if (null === $this->_sourceData) {
			$this->_sourceData = $this->_productCategoryModel->getSourceData();
		}
		return (isset($this->_sourceData[$type]) && isset($this->_sourceData[$type][$param]))?
			$this->_sourceData[$type][$param] : $default;
	}

	protected function _hasDate($type)
	{
		return $this->isEnabled()
			&& ($this->_param($type, 'from') || $this->_param($type, 'text'));
	}
	public function hasDeliveryDate() { return $this->_hasDate('delivery'); }
	public function hasShippingDate() { return $this->_hasDate('shipping'); }


	protected function _formatDate($type)
	{
		return ($this->isEnabled() 
			&& $this->_hasDate($type)
			&& $this->_param($type, 'from')) ? 'date': 'text';
	}
	public function formatDeliveryDate() { return $this->_formatDate('delivery'); }
	public function formatShippingDate() { return $this->_formatDate('shipping'); }


	protected function _getTime($type, $dir)
	{
		if ($this->isEnabled()) {
			return strtotime($this->_param($type, $dir));
		}
		return 0;
	}
	public function getDeliveryFromTime() { return $this->_getTime('delivery', 'from'); }
	public function getShippingFromTime() { return $this->_getTime('shipping', 'from'); }

	public function getDeliveryToTime() { return $this->_getTime('delivery', 'to'); }
	public function getShippingToTime() { return $this->_getTime('shipping', 'to'); }

	public function getDeliveryTime() { return $this->getDeliveryFromTime(); }
	public function getShippingTime() { return $this->getShippingFromTime(); }
	

	protected function _getDate($type, $dir)
	{
		if ($this->isEnabled()) {
			return $this->_param($type, $dir);
		}
		return '';
	}
	public function getDeliveryFromDate() { return $this->_getDate('delivery', 'from'); }
	public function getShippingFromDate() { return $this->_getDate('shipping', 'from'); }

	public function getDeliveryToDate() { return $this->_getDate('delivery', 'to'); }
	public function getShippingToDate() { return $this->_getDate('shipping', 'to'); }

	public function getDeliveryDate() { return $this->getDeliveryFromDate(); }
	public function getShippingDate() { return $this->getShippingFromDate(); }
	

	protected function _getText($type)
	{
		if ($this->isEnabled()) {
			$process = $this->_objectManager->get('Magento\Cms\Model\Template\FilterProvider')->getPageFilter();
	    	return $process->filter($this->_param($type, 'text'));
		}
		return '';
	}
	public function getDeliveryText() { return $this->_getText('delivery'); }
	public function getShippingText() { return $this->_getText('shipping'); }

	public function specialFormatDate($time)
	{
		// $locale = new \Zend_Locale($this->_localeResolver->getLocale());
		// $date = new \Zend_Date($time, false, $locale);
		// return $this->_localeDate->formatDateTime();

		$date = $this->_localeDate->date($time);

		return $this->_localeDate->formatDate(
            $date,
            \IntlDateFormatter::SHORT,
            false
        );
	}
	
	/* deprecated function do not delete */
	public function getEstimatedDate() { return $this->getDeliveryTime(); }
	public function getEstimatedText() { return $this->getDeliveryText(); }

	public function getShppingFromTime() { return $this->getShippingFromTime(); }
	public function getShppingToTime() { return $this->getShippingToTime(); }

	public function getDelivery()
	{
		$value = '';
		if ($this->hasDeliveryDate()) {
			if ($this->formatDeliveryDate() == 'date') { 
				$value = $this->specialFormatDate( $this->getDeliveryFromTime() );

				if ($this->getDeliveryToTime() && ($this->getDeliveryToTime() != $this->getDeliveryFromTime())) {
					 $value .= ' - ' . $this->specialFormatDate( $this->getDeliveryToTime() );
				}
			} else {
				$value = $this->getDeliveryText();
			}
		}

		if($value) {
			return [
				'label' => __('Estimated Delivery Date'),
				'value' => $value,
				'custom_view' => true,
			];
		}
	}


	public function getShipping()
	{
		$value = '';
		if ($this->hasShippingDate()) {
			if ($this->formatShippingDate() == 'date') { 
				$value = $this->specialFormatDate( $this->getShippingFromTime() );
				if ($this->getShippingToTime() && ($this->getShippingToTime() != $this->getShippingFromTime())) {
					 $value .= ' - ' . $this->specialFormatDate( $this->getShippingToTime() );
				}
			} else {
				$value = $this->getShippingText();
			}
		}

		if($value) {
			return [
				'label' => __('Estimated Shipping Date'),
				'value' => $value,
				'custom_view' => true,
			];
		}
	}
}
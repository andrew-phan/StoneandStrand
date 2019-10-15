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

namespace Ss\Sales\Helper;

class Item extends \Magento\Framework\App\Helper\AbstractHelper
{
    // Estimate time has 5 status
    const STATUS_STATIC_DATE = 4; // Value of option static date of estimate time
    const STATUS_STATIC_DATE_RANGE = 5; // Value of option static date range of estimate time
    
    // Estimate time has 2 stype
    const STYPE_DATE = 'date'; // stype of estimate time is date
    const STYPE_TEXT = 'text'; // stype of estimate time is text

    protected $_sourceData;
    protected $_helper;
    protected $_productCategoryModel;
    protected $_objectManager;
    protected $_localeResolver;
    protected $_orderModel;

    public function __construct(
        \Plumrocket\Estimateddelivery\Helper\Data $helper,
        \Plumrocket\Estimateddelivery\Model\ProductCategory $productCategoryModel,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);

        $this->_helper = $helper;
        $this->_productCategoryModel = $productCategoryModel;
        $this->_objectManager = $objectManager;
        $this->_localeResolver = $localeResolver;
    }

    public function isEnabled() {
        return $this->_helper->moduleEnabled();
    }

    public function setCategory($category) {
        $this->_productCategoryModel->setCategory($category);
        return $this;
    }

    public function setProduct($product) {
        $this->reset();
        $this->_productCategoryModel->setProduct($product);
        return $this;
    }

    /**
     * 
     * @param \Magento\Sales\Model\Order $order
     * @return \Ss\Sales\Helper\Item
     */
    public function setOrder($order) {
        $this->_orderModel = $order;
        return $this;
    }

    public function reset() {
        $this->_productCategoryModel->reset();
        $this->_sourceData = null;
        return $this;
    }

    public function getCategory() {
        return $this->_productCategoryModel->getCategory();
    }

    public function getProduct() {
        return $this->_productCategoryModel->getProduct();
    }

    public function getOrder() {
        return $this->_orderModel;
    }

    // protected  ---------

    protected function _param($type, $param, $default = false) {
        if (null === $this->_sourceData) {
            $this->_sourceData = $this->_productCategoryModel->getSourceData();
        }
        return (isset($this->_sourceData[$type]) && isset($this->_sourceData[$type][$param])) ?
                $this->_sourceData[$type][$param] : $default;
    }

    protected function _hasDate($type) {
        return $this->isEnabled() && ($this->_param($type, 'from') || $this->_param($type, static::STYPE_TEXT));
    }

    protected function _formatDate($type) {
        return ($this->isEnabled() && $this->_hasDate($type) && $this->_param($type, 'from')) ? static::STYPE_DATE : static::STYPE_TEXT;
    }

    protected function _getTime($type, $dir) {
        if ($this->isEnabled()) {
            return strtotime($this->_param($type, $dir));
        }
        return 0;
    }

    protected function _getDate($type, $dir) {
        if ($this->isEnabled()) {
            return $this->_param($type, $dir);
        }
        return '';
    }

    protected function _getText($type) {
        if ($this->isEnabled()) {
            $process = $this->_objectManager->get('Magento\Cms\Model\Template\FilterProvider')->getPageFilter();
            return $process->filter($this->_param($type, static::STYPE_TEXT));
        }
        return '';
    }

    public function specialFormatDate($time, $status, $checkComplete = true) {
        $date = $time;
        $now = time();
        
        if ($status != static::STATUS_STATIC_DATE && $status != static::STATUS_STATIC_DATE_RANGE) {
            $OrderCreatedAt = strtotime($this->_orderModel->getCreatedAt());
            
            $date -= ($now - $OrderCreatedAt);
        }
        if ($checkComplete && $date <= $now) {
            return '';
        }
        
        $date = date('m/d/Y', $date);
        return $date;
    }

    /* deprecated function do not delete */
    public function getDelivery() {
        $value = '';
        $deliveryLabel = 'delivery';
        
        if ($this->_hasDate($deliveryLabel)) {
            if ($this->_formatDate($deliveryLabel) == static::STYPE_DATE) {
                $status = $this->getProduct()->getEstimatedDeliveryEnable();
                $deliveryFromTime = $this->_getTime($deliveryLabel, 'from');
                $deliveryToTime = $this->_getTime($deliveryLabel, 'to');
                
                if ($deliveryToTime && ($deliveryToTime != $deliveryFromTime)) {
                    $deliveryToTime = $this->specialFormatDate($deliveryToTime, $status);
                    
                    $value = empty($deliveryToTime) ? __("Complete") : $this->specialFormatDate($deliveryFromTime, $status, FALSE) . ' - ' . $deliveryToTime;
                } else {
                    $value = $this->specialFormatDate($deliveryFromTime, $status);
                }
            } else {
                $value = $this->_getText($deliveryLabel);
            }
        }

        if ($value) {
            return [
                'label' => __('Estimated Delivery Date'),
                'value' => $value,
                'custom_view' => true,
            ];
        }
    }

    public function getShipping() {
        $value = '';
        $shippingLabel = 'shipping';
        
        if ($this->_hasDate($shippingLabel)) {
            if ($this->_formatDate($shippingLabel) == static::STYPE_DATE) {
                $status = $this->getProduct()->getEstimatedShippingEnable();
                $shippingFromTime = $this->_getTime($shippingLabel, 'from');
                $shippingToTime = $this->_getTime($shippingLabel, 'to');
                
                if ($shippingToTime && ($shippingToTime != $shippingFromTime)) {
                    $shippingToTime = $this->specialFormatDate($shippingToTime, $status);
                    
                    $value = empty($shippingToTime) ? __("Complete") : $this->specialFormatDate($shippingFromTime, $status, FALSE) . ' - ' . $shippingToTime;
                } else {
                    $value = $this->specialFormatDate($shippingFromTime, $status);
                }
            } else {
                $value = $this->_getText($shippingLabel);
            }
        }

        if ($value) {
            return [
                'label' => __('Estimated Shipping Date'),
                'value' => $value,
                'custom_view' => true,
            ];
        }
    }
}

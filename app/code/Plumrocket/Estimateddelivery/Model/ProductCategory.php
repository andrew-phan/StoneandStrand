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

namespace Plumrocket\Estimateddelivery\Model;

class ProductCategory extends \Magento\Framework\Model\AbstractModel
{
    const INHERITED = 0;
    const DISABLED = 1;
    const DYNAMIC_DATE = 2;
    const DYNAMIC_RANGE = 3;
    const STATIC_DATE = 4;
    const STATIC_RANGE = 5;
    const TEXT = 6;

    protected $_result = null;
    protected $_dateEnd = '';

    protected $_helper = null;
    protected $_bankday = null;
    protected $_productModel = null;
    protected $_categoryModel = null;
    protected $_dateTime = null;

    protected $_product = null;
    protected $_category = null;

    public function __construct(
        \Plumrocket\Estimateddelivery\Helper\Data $helper,
        \Plumrocket\Estimateddelivery\Helper\Bankday $bankday,
        \Magento\Catalog\Model\ProductFactory $productModel,
        \Magento\Catalog\Model\CategoryFactory $categoryModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,

        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_helper = $helper;
        $this->_bankday = $bankday;
        $this->_productModel = $productModel;
        $this->_categoryModel = $categoryModel;
        $this->_categoryModel = $categoryModel;
        $this->_dateTime = $dateTime;
    }

    public function getProduct()
    {
        if (null === $this->_product) {
            $this->_product = $this->_registry->registry('product');

            if (!$this->_product || !$this->_product->getId()) {
                $this->_product = $this->_productModel->create();
            }
        }
        return $this->_product;
    }

    public function getCategory()
    {
        if (null === $this->_category) {
            $this->_category = $this->_registry->registry('current_category');

            if (!$this->_category || !$this->_category->getId()) {
                $this->_category = $this->_categoryModel->create();
            }
        }
        return $this->_category;   
    }

    public function setProduct($product)
    {
        $this->reset();

        if(null === $product->getData('estimated_delivery_enable') && null === $product->getData('estimated_shipping_enable') && $product->getId()) {
            $product = $this->_productModel->create()->load($product->getId());
        }
        
        $this->_product = $product;
    }

    public function setCategory($category)
    {
        $this->reset();
        $this->_category = $category;
    }

    public function reset()
    {
        $this->_result = null;
        $this->_dateEnd = '';
        $this->_category = null;
        $this->_product = null;
    }

    public function getSourceData()
    {   
        if (!$this->_result) {
            $this->_result = [];

            if ($this->_helper->moduleEnabled()) {
                $this->_result = [
                    'delivery' => $this->_getData('delivery'),
                    'shipping' => $this->_getData('shipping')
                ];
            }
        }
        return $this->_result;
    }
    
    // ---- Private functions
    protected function _getData($type)
    {
        $product = $this->getProduct();
        if ($product && $product->getId()) {
            $result = $this->_getDataFromProduct($product, $type);
        } else {
            $category = $this->getCategory();
            $result = $this->_getDataFromCategory($category, $type);
        }

        return $result;
    }

    protected function _getDataFromProduct($product, $type)
    {
        $result = self::INHERITED;

        if ($this->_value($product, $type, 'enable') != self::INHERITED) {
            return $this->_parseData($product, $type);
        } else {
            // scan categories
            $cIds = $product->getCategoryIds();
            if ($cIds) {
                // foreach by all parents' categories of product and check if any parent set or him parents
                foreach ($cIds as $cid) {

                    $cat = $this->_categoryModel->create()->load($cid);
                    $res = $this->_getDataFromCategory($cat, $type);

                    // if at least parent is enabled then product is enabled
                    // else return will be 0 - inherited or False - disable
                    if ($res) {
                        $result = $res;
                        break;
                    }
                    // If at end all parents will be inherited exept one or each disabled 
                    // then product will be disabled
                    if ($res === false) {
                        $result = false;
                    }
                }
            }
        }
        return $result;
    }

    protected function _getDataFromCategory($cat, $type)
    {
        $result = self::INHERITED;
        $parentIds = $cat->getParentIds();
        
        do {
            if ($cat && $cat->getId() && $cat->getIsActive()) {
                if ($this->_value($cat, $type, 'enable') != self::INHERITED) {
                    $result = $this->_parseData($cat, $type);    
                    break;
                }
            }

            $pid = array_pop($parentIds);
            if ($pid) {
                $cat = $this->_categoryModel->create()->load($pid);
            }
        } while ($pid);

        return $result;
    }

    protected function _parseData($object, $type)
    {
        $result = ['from' => '', 'to' => '', 'text' => ''];
        $enable = $this->_value($object, $type, 'enable');

        switch ($enable) {
            case self::DYNAMIC_RANGE:
                $result['to'] = $this->_formatDate( $this->_value($object, $type, 'days_to'), $type );
            case self::DYNAMIC_DATE:
                $result['from'] = $this->_formatDate( $this->_value($object, $type, 'days_from'), $type );
                break;

            case self::STATIC_RANGE:
                $result['to'] = str_replace('00:00:00' , '12:00:00', $this->_value($object, $type, 'date_to'));
            case self::STATIC_DATE:
                $result['from'] = str_replace('00:00:00' , '12:00:00', $this->_value($object, $type, 'date_from'));
                break;

            case self::TEXT:
                $result['text'] = $this->_value($object, $type, 'text');
                break;

            case self::DISABLED:
            default:
                $result = false;
                break;
        }

        if(false !== $result) {
            if (empty($result['text']) && $this->_helper->getConfig($this->_helper->getConfigSectionId() . '/' . $type . '/default_text_enable')) {
                $result['text'] = trim($this->_helper->getConfig($this->_helper->getConfigSectionId() . '/' . $type . '/default_text'));
            }
        }

        return $result;
    }

    protected function _value($object, $type, $param)
    {
        return $object->getData( $this->_param($type, $param) );
    }

    protected function _param($type, $param)
    {
        return 'estimated_' . $type . '_' . $param;
    }

    protected function _formatDate($value, $type)
    {
        $time = $this->_dateTime->timestamp();

        return strftime(
            '%Y-%m-%d %H:%M:%S',
            $this->_bankday->getEndDate($type, $time, (int)$value)
        );
    }
}

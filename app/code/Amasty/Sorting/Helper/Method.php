<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Helper;

use Amasty\Sorting\Api\IndexedMethodInterface;

/**
 * Class Data
 *
 * @package Amasty\Sorting\Helper
 */
class Method
{
    /**
     * @var array
     */
    protected $_methodsNames
        = ['NewMethod',
           'Saving',
           'Bestselling',
           'MostViewed',
           'Toprated',
           'Commented',
           'Wished'];

    /**
     * @var array
     */
    protected $_methods = [];

    /**
     * @var array
     */
    protected $_indexedMethods = [];

    /**
     * @var boolean
     */
    protected $_indexedMethodsExists = true;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function getIndexedMethods()
    {
        if (empty($this->_indexedMethods) && $this->_indexedMethodsExists) {
            $this->getMethods();
        }
        foreach ($this->_indexedMethods as $code => $data) {
            $this->_indexedMethods[$code]['object']
                = $this->_objectManager->get($data['class']);
        }
        return $this->_indexedMethods;
    }

    /**
     * @return array
     */
    public function getMethods($isConfig = false)
    {
        if (empty($this->_methods)) {
            foreach ($this->_methodsNames as $className) {

                $class = 'Amasty\Sorting\Model\Method\\' . $className;

                if (!$this->isEnabledMethod($className, $isConfig)) {
                    continue;
                }
                $data = [
                    'code'  => $class::CODE,
                    'name'  => $class::NAME,
                    'class' => $class
                ];
                if (is_subclass_of(
                    $class, 'Amasty\Sorting\Api\IndexedMethodInterface', true
                )) {
                    $this->_indexedMethods[$class::CODE] = $data;
                }
                if (is_subclass_of(
                    $class, 'Amasty\Sorting\Api\MethodInterface', true
                )) {
                    $this->_methods[$class::CODE] = $data;
                }
            }
            if (empty($this->_indexedMethods)) {
                $this->_indexedMethodsExists = false;
            }
        }

        return $this->_methods;
    }

    public function isEnabledMethod($className, $isConfig = false)
    {
        $classPath = 'Amasty\Sorting\Model\Method\\' . $className;
        $enabled = $classPath::ENABLED;
        if (!$enabled) {
            return false;
        }
        if (!$isConfig) {
            $disabled = $this->_scopeConfig->getValue(
                'amsorting/general/disable_methods',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            return (false === in_array(
                    $classPath::CODE, explode(',', $disabled)
                ));
        }
        return true;

    }

    public function applyMethodByToolbar(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject
    ) {
        $order = $subject->getCurrentOrder();
        $method = $this->getMethodByCode($order);
        if ($method instanceof \Amasty\Sorting\Api\MethodInterface) {
            $method->apply(
                $subject->getCollection(), $subject->getCurrentDirection()
            );
        }
    }

    public function getMethodByCode($code)
    {
        if(!isset($this->_methods[$code])){
            return null;
        }
        if (!isset($this->_methods[$code]['object'])) {
            $class = $this->_methods[$code]['class'];
            $this->_methods[$code]['object'] = $this->_objectManager->get(
                $class
            );
        }
        return $this->_methods[$code]['object'];
    }

}
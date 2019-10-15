<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Block\Catalog\Product\ProductList;

/**
 * Class Toolbar
 *
 * @package Amasty\Sorting\Block\Catalog\Product\ProductList
 */
class Toolbar
{
    /**
     * @var null
     */
    protected $methods = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Amasty\Sorting\Helper\Method
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Catalog\Model\Product\ProductList\Toolbar
     */
    protected $_toolbarModel;


    /**
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Amasty\Sorting\Helper\Method                      $helper
     * @param \Magento\Framework\App\RequestInterface            $request
     * @param \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\Sorting\Helper\Method $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel
    ) {
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_request = $request;
        $this->_toolbarModel = $toolbarModel;
    }

    /**
     * @param string $order
     *
     * @return bool
     */
    protected function reverse($order)
    {
        $methods = $this->_helper->getMethods();
        if (isset($methods[$order])) {
            return true;
        }
        $attr = $this->_scopeConfig->getValue(
            'amsorting/general/desc_attributes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($attr) {
            return in_array($order, explode(',', $attr));
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param string                                             $dir
     *
     * @return string
     */
    public function afterGetCurrentDirection($subject, $dir)
    {
        $selectedDirection = strtolower($this->_toolbarModel->getDirection());
        if (!$selectedDirection
            && $this->reverse($subject->getCurrentOrder())
        ) {
            $subject->setDefaultDirection('desc');
            $dir = 'desc';
        }
        return $dir;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function afterSetCollection($subject)
    {
        if ($subject->getFlag('amsorting')) {
            return $subject;
        }

        // no image sorting will be the first or the second (after stock). LIFO queue
        $hasImage = $this->_objectManager->get(
            'Amasty\Sorting\Model\Method\Image'
        );
        $hasImage->apply($subject->getCollection(), '');

        // in stock sorting will be first, as the method always moves it's paremater first. LIFO queue
        $inStock = $this->_objectManager->get(
            'Amasty\Sorting\Model\Method\Instock'
        );
        $inStock->apply($subject->getCollection(), '');

        $this->_helper->applyMethodByToolbar($subject);

        if ($this->_request->getParam('debug')) {
            echo $subject->getCollection()->getSelect();
        }

        $subject->setFlag('amsorting', 1);

        return $subject;
    }

}
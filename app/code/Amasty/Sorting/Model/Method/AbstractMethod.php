<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

use Amasty\Sorting\Api\MethodInterface;

/**
 * Class AbstractMethod
 *
 * @package Amasty\Sorting\Model\Method
 */
abstract class AbstractMethod extends \Magento\Framework\DataObject implements MethodInterface
{
    /**
     *
     */
    const CODE = '';

    /**
     *
     */
    const NAME = '';

    /**
     * @var bool
     */
    const ENABLED = true;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $_productMetaData;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_request = $context->getRequest();
        $this->_resource = $context->getResource();
        $this->_objectManager = $context->getObjectManager();
        $this->_productMetaData = $context->getProductMetaData();
        $this->_moduleManager = $context->getModuleManager();
        $this->_storeManager = $context->getStoreManager();
    }

    abstract public function apply($collection,$direction);
}
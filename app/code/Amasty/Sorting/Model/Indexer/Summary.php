<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Indexer;

use Psr\Log\LoggerInterface;

/**
 * Class Summary
 *
 * @package Amasty\Sorting\Model\Indexer
 */
class Summary
{
    /**
     * @var \Amasty\Sorting\Helper\Method
     */
    protected $_helper;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Amasty\Sorting\Helper\Method                      $helper
     * @param LoggerInterface                                    $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Amasty\Sorting\Helper\Method $helper,
        LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return void
     */
    public function reindexAll()
    {
        if (!$this->_scopeConfig->getValue(
            'amsorting/general/use_index',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return;
        }

        $methods = $this->_helper->getIndexedMethods();
        foreach ($methods as $method) {
            $method['object']->reindex();
        }

    }
}
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
class Index extends \Magento\Framework\App\Helper\AbstractHelper
{
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
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param $field
     * @param $settingKey
     *
     * @return bool|int|string
     */
    public function getPeriodCondition($field, $settingKey)
    {
        $period = intVal(
            $this->_scopeConfig->getValue(
                'amsorting/' . $settingKey,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
        if ($period) {
            $period = date('Y-m-d', time() - $period * 24 * 3600);
            $period = " AND $field > '$period' ";
        } else {
            $period = '';
        }

        return $period;
    }

    /**
     * @param string $field
     *
     * @return string
     */
    public function getStoreCondition($field)
    {
        return " AND $field = " . $this->_storeManager->getStore()->getId();
    }
}
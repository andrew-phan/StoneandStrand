<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

/**
 * Class Instock
 *
 * @package Amasty\Sorting\Model\Method
 */
class Instock extends AbstractMethod
{
    /**
     * @param $collection
     * @param $currDir
     *
     * @return $this
     */
    public function apply($collection, $currDir)
    {
        $show = $this->_scopeConfig->getValue(
            'amsorting/general/out_of_stock_last',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$show) {
            return $this;
        }

        //skip search results    
        $isSearch = in_array(
            $this->_request->getModuleName(),
            array('sqli_singlesearchresult', 'catalogsearch')
        );
        if ($isSearch && 2 == $show) {
            return $this;
        }

        $select = $collection->getSelect();

        if (false === strpos($select->__toString(), 'cataloginventory_stock_status')) {
            $website = $this->_storeManager->getWebsite();
            $this->_objectManager->get(
                'Magento\CatalogInventory\Model\ResourceModel\Stock\Status'
            )
                ->addStockStatusToSelect($select, $website);

        }

        $field = 'salable desc';
        if ($this->_scopeConfig->getValue(
            'amsorting/general/out_of_stock_qty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            $field = new \Zend_Db_Expr('IF(stock_status.qty > 0, 0, 1)');
        }
        $select->order($field);

        // move to the first position
        $orders = $select->getPart(\Zend_Db_Select::ORDER);
        if (count($orders) > 1) {
            $last = array_pop($orders);
            array_unshift($orders, $last);
            $select->setPart(\Zend_Db_Select::ORDER, $orders);
        }

        return $this;
    }
}
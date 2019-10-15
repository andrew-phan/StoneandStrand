<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

use Amasty\Sorting\Api\IndexedMethodInterface;
use Magento\Reports\Model\ResourceModel\Product\Index\AbstractIndex;

/**
 * Class Bestselling
 *
 * @package Amasty\Sorting\Model\Method
 */
class Bestselling extends AbstractIndexMethod
{
    const CODE = 'bestsellers';

    const NAME = 'Best Sellers';

    /**
     * @var bool
     */
    protected $isAllGrouped = false;

    /**
     * @return \Zend_Db_Expr
     */
    public function getColumnSelect()
    {
        $sql = ' SELECT SUM(order_item.qty_ordered)'
            . ' FROM ' . $this->_resource->getTableName(
                'sales_order_item'
            )
            . ' AS order_item';

        // exclude items (products)
        $exclude = $this->_scopeConfig->getValue(
            'amsorting/bestsellers/exclude',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($exclude) {
            $sql .= ' LEFT JOIN ' . $this->_resource
                    ->getTableName('sales_order')
                . ' AS flat_orders'
                . '   ON (flat_orders.entity_id = order_item.order_id) ';
        }

        if ($this->isAllGrouped) {
            $sql .= ' INNER JOIN ' . $this->_resource
                    ->getTableName('quote_item_option')
                . ' AS order_item_option'
                . '   ON (order_item.item_id = order_item_option.item_id AND order_item_option.code="product_type")'
                . ' WHERE order_item_option.product_id = e.entity_id ';
        } else {
            $sql .= ' WHERE order_item.product_id = e.entity_id ';
        }

        if ($exclude) {
            $temp = explode(',', $exclude);
            $exclude = implode('\',\'', $temp);
            $sql .= ' AND flat_orders.status NOT IN (\'' . $exclude . '\')';
        }

        $sql .= $this->_indexHelper->getPeriodCondition(
            'order_item.created_at', 'bestsellers/best_period'
        );
        $sql .= $this->_indexHelper->getStoreCondition('order_item.store_id');


        return new \Zend_Db_Expr('(' . $sql . ')');
    }

    /**
     * @return string
     */
    public function getIndexSelect()
    {
        $sql = ' SELECT product_id, order_item.store_id, SUM(qty_ordered)'
            . ' FROM ' . $this->_resource->getTableName(
                'sales_order_item'
            ) . ' AS order_item';

        // exclude items (products)
        $exclude = $this->_scopeConfig->getValue(
            'amsorting/bestsellers/exclude',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $temp = explode(',', $exclude);
        $exclude = implode('\',\'', $temp);
        if ($exclude) {
            $sql .= ' LEFT JOIN ' . $this->_resource
                    ->getTableName('sales_order')
                . ' AS flat_orders'
                . '   ON (flat_orders.entity_id = order_item.order_id) '
                . ' WHERE flat_orders.status NOT IN (\'' . $exclude . '\')';
        } else {
            $sql .= ' WHERE 1 ';
        }

        $sql .= $this->_indexHelper->getPeriodCondition(
                'order_item.created_at', 'bestsellers/best_period'
            )
            . ' GROUP BY product_id'//, store_id'
        ;

        if ($this->isAllGrouped) {
            $sql
                = ' SELECT order_item_option.product_id, order_item.store_id, SUM(order_item.qty_ordered)'
                . ' FROM ' . $this->_resource->getTableName(
                    'sales_order_item'
                ) . ' AS order_item';
            if ($exclude) {
                $sql .= ' LEFT JOIN ' . $this->_resource
                        ->getTableName('sales_order')
                    . ' AS flat_orders'
                    . '   ON (flat_orders.entity_id = order_item.order_id) '
                    . ' WHERE flat_orders.status NOT IN (\'' . $exclude . '\')';
            }
            $sql .= ' INNER JOIN ' . $this->_resource
                    ->getTableName('quote_item_option')
                . ' AS order_item_option'
                . '   ON (order_item.item_id = order_item_option.item_id AND order_item_option.code="product_type")';
            if (!$exclude) {
                $sql .= ' WHERE 1 ';
            }
            $sql .= $this->_indexHelper->getPeriodCondition(
                    'order_item.created_at', 'bestsellers/best_period'
                )
                . ' GROUP BY order_item_option.product_id'//, order_item.store_id'
            ;
        }
        //file_put_contents('sql',$sql,FILE_APPEND);
        return $sql;
    }

    /**
     * @param $collection
     * @param $currDir
     *
     * @return $this
     */
    public function apply($collection, $currDir)
    {
        $attr = $this->_scopeConfig->getValue(
            'amsorting/bestsellers/best_attr',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($attr) {
            $collection->addAttributeToSort($attr, $currDir);
        }
        return parent::apply($collection, $currDir);
    }
}
<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

/**
 * Class Wished
 *
 * @package Amasty\Sorting\Model\Method
 */
class Wished extends AbstractIndexMethod
{
    const CODE = 'wished';

    const NAME = 'Now in Wishlists';

    /**
     * @return \Zend_Db_Expr
     */
    public function getColumnSelect()
    {
        $sql = ' SELECT COUNT(*)'
            . ' FROM ' . $this->_resource->getTableName(
                'wishlist_item'
            ) . ' AS wishlist_item'
            . ' WHERE wishlist_item.product_id = e.entity_id '
            . $this->_indexHelper->getStoreCondition('wishlist_item.store_id');
        return new \Zend_Db_Expr('(' . $sql . ')');
    }

    /**
     * @return string
     */
    public function getIndexSelect()
    {
        $sql = ' SELECT product_id, store_id, COUNT(*)'
            . ' FROM ' . $this->_resource->getTableName(
                'wishlist_item'
            )
            . ' GROUP BY product_id, store_id';
        return $sql;
    }
}
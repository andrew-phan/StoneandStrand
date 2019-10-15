<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

/**
 * Class MostViewed
 *
 * @package Amasty\Sorting\Model\Method
 */
class MostViewed extends AbstractIndexMethod
{
    const CODE = 'most_viewed';

    const NAME = 'Most Viewed';
    
    /**
     * @return \Zend_Db_Expr
     */
    public function getColumnSelect()
    {
        $sql = ' SELECT COUNT(*)'
            . ' FROM ' . $this->_resource->getTableName('report_event') . ' AS viewed_item'
            . ' WHERE viewed_item.object_id = e.entity_id AND viewed_item.event_type_id = '
            . \Magento\Reports\Model\Event::EVENT_PRODUCT_VIEW
            . $this->_indexHelper->getPeriodCondition(
                'viewed_item.logged_at', 'most_viewed/viewed_period'
            )
            . $this->_indexHelper->getStoreCondition('viewed_item.store_id');

        return new \Zend_Db_Expr('(' . $sql . ')');
    }

    /**
     * @return string
     */
    public function getIndexSelect()
    {
        $sql = ' SELECT object_id, store_id, COUNT(*)'
            . ' FROM ' . $this->_resource->getTableName('report_event') . ' AS viewed_item'
            . ' WHERE viewed_item.event_type_id = '
            . \Magento\Reports\Model\Event::EVENT_PRODUCT_VIEW
            . $this->_indexHelper->getPeriodCondition(
                'viewed_item.logged_at', 'most_viewed/viewed_period'
            )
            . ' GROUP BY object_id, store_id';
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
            'amsorting/most_viewed/viewed_attr',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($attr) {
            $collection->addAttributeToSort($attr, $currDir);
        }
        return parent::apply($collection, $currDir);
    }
}
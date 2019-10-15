<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

/**
 * Class Image
 *
 * @package Amasty\Sorting\Model\Method
 */
class Image extends AbstractMethod
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
            'amsorting/general/no_image_last',
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

        $collection->addAttributeToSort('small_image', 'asc');

        $orders = $collection->getSelect()->getPart(\Zend_Db_Select::ORDER);

        // move from the last to the the first position
        $last = array_pop($orders);
        $last[0] = new \Zend_Db_Expr(
            'IF(IFNULL(`small_image`, "no_selection")="no_selection", 1, 0)'
        );
        array_unshift($orders, $last);

        $collection->getSelect()->setPart(\Zend_Db_Select::ORDER, $orders);

        return $this;
    }
}
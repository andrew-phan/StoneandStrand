<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

/**
 * Class NewMethod
 *
 * @package Amasty\Sorting\Model\Method
 */
class NewMethod extends AbstractMethod
{
    const CODE = 'created_at';

    const NAME = 'New';

    /**
     * @param $collection
     * @param $currDir
     *
     * @return $this
     */
    public function apply($collection, $currDir)
    {
        $new = false;
        $orders = $collection->getSelect()->getPart(\Zend_Db_Select::ORDER);
        foreach ($orders as $k => $v) {
            if (is_object($v)) {
                continue;
            } elseif (false !== strpos($v[0], 'created_at')) {
                $new = $k;
            }
        }

        $attr = $this->_scopeConfig->getValue(
            'amsorting/new/new_attr',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($attr) {
            if ($new) {
                $orders[$new] = null;
                unset($orders[$new]);
            }
            $collection->getSelect()->setPart(\Zend_Db_Select::ORDER, $orders);
            $collection->addAttributeToSort($attr, $currDir);
        } elseif (!$new) {
            $collection->addAttributeToSort('created_at', $currDir);
        }

        return $this;
    }
}
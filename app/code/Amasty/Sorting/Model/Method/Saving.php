<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

/**
 * Class Saving
 *
 * @package Amasty\Sorting\Model\Method
 */
class Saving extends AbstractMethod
{
    const CODE = 'saving';

    const NAME = 'Biggest Saving';

    /**
     * @param $collection
     * @param $currDir
     *
     * @return $this
     */
    public function apply($collection, $currDir)
    {
       // die('king');
        $alias = 'price_index';
        if (preg_match(
            '/`([a-z0-9\_]+)`\.`final_price`/',
            $collection->getSelect()->__toString(), $m
        )) {
            $alias = $m[1];
        }

        $storeId = $this->_storeManager->getStore()->getId();
        if ($this->_scopeConfig->getValue(
            'amsorting/biggest_saving/saving',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            $collection->getSelect()->columns(
                array('saving' => "IF(`$alias`.price, ((`$alias`.price - IF(`$alias`.tier_price IS NOT NULL, LEAST(`$alias`.min_price, `$alias`.tier_price), `$alias`.min_price)) * 100 / `$alias`.price), 0)")
            );
        } else {
            $collection->getSelect()->columns(
                array('saving' => "(`$alias`.price - IF(`$alias`.tier_price IS NOT NULL, LEAST(`$alias`.min_price, `$alias`.tier_price), `$alias`.min_price))")
            );
        }

        $collection->getSelect()->order("saving $currDir");

        return $this;
    }
}
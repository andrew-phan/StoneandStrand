<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

/**
 * Class Toprated
 *
 * @package Amasty\Sorting\Model\Method
 */
class Toprated extends AbstractMethod
{
    const CODE = 'rating_summary';

    const NAME = 'Top Rated';

    /**
     * @param \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection
     * @param string                                                    $currDir
     *
     * @return $this
     */
    public function apply($collection, $currDir)
    {
        $collection->joinField(
            static::CODE,               // alias
            'review_entity_summary',      // table
            static::CODE,               // field
            'entity_pk_value=entity_id',    // bind
            array(
                'entity_type' => 1,
                'store_id'    => $this->_storeManager->getStore()->getId()
            ),                              // conditions
            'left'                          // join type
        );
        $collection->getSelect()->order(static::CODE . ' ' . $currDir);

        return $this;
    }
}
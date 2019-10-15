<?php

namespace Ss\Collection\Model\ResourceModel\Collection;

/**
 * Subscription Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ss\Collection\Model\Collection', 'Ss\Collection\Model\ResourceModel\Collection');
    }

}

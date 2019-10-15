<?php

namespace Ss\Slideshow\Model\ResourceModel;

/**
 * Resource banner.
 */
class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function _construct()
    {
        $this->_init('ss_slideshow_banner', 'banner_id');
    }

}

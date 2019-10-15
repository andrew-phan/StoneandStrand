<?php

namespace Ss\Designer\Model\ResourceModel;

class Tags extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function _construct()
    {
        $this->_init('ss_designer_tags', 'tag_id');
    }

}

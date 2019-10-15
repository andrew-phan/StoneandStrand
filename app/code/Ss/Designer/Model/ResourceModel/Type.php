<?php

namespace Ss\Designer\Model\ResourceModel;

class Type extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function _construct()
    {
        $this->_init('ss_designer_type', 'type_id');
    }

}

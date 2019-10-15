<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Model\Override\Storage;

use Magento\Framework\App\ResourceConnection;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\Framework\Api\DataObjectHelper;

class DbStorage extends \Magento\UrlRewrite\Model\Storage\DbStorage
{

    /**
     * Prepare select statement for specific filter
     *
     * @param array $data
     * @return \Magento\Framework\DB\Select
     */
    public function ssPrepareSelect($data)
    {
        return $this->prepareSelect($data);
    }

    /**
     * Insert multiple
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Exception
     */
    public function ssInsertMultiple($data)
    {
        $this->insertMultiple($data);
    }

    /**
     * @todo to get column alias
     * @param type $column
     * @return type
     */
    public function getColumnName($column)
    {
        return $this->connection->quoteIdentifier($column);
    }

    /**
     * @todo to Remove data from select
     * @param type $select
     */
    public function ssDeleteFromSelect($select)
    {
        $this->connection->query(
            $select->deleteFromSelect($this->resource->getTableName(self::TABLE_NAME))
        );
    }

    /**
     * @todo To find Id url rewrite
     * @param type $data
     * @return type
     */
    public function ssFindIdUrlRewrite($data)
    {
        $select = $this->prepareSelect($data);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('url_rewrite_id')->limit(1);
        return $this->connection->fetchRow($select);
    }
    
    /**
     * @todo ssFindUrlRewriteProduct
     */
    public function ssFindUrlRewriteProduct($select){
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('url_rewrite_id')->limit(1);
        return $this->connection->fetchRow($select);
    }

}

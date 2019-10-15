<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Method;

use Amasty\Sorting\Api\IndexedMethodInterface;
use Amasty\Sorting\Api\MethodInterface;

/**
 * Class AbstractMethod
 *
 * @package Amasty\Sorting\Model\Method
 */
abstract class AbstractIndexMethod extends AbstractMethod
    implements IndexedMethodInterface
{
    /**
     * @var \Amasty\Sorting\Helper\Index
     */
    protected $_indexHelper;

    /**
     * @param \Amasty\Sorting\Helper\Index $indexHelper
     * @param Context                      $context
     */
    public function __construct(
        \Amasty\Sorting\Helper\Index $indexHelper,
        Context $context
    ) {
        parent::__construct($context);
        $this->_indexHelper = $indexHelper;
    }

    abstract public function getColumnSelect();

    abstract public function getIndexSelect();

    /**
     * @return bool
     */
    public function reindex()
    {
        $table = $this->getIndexTable();

        $db = $this->_resource->getConnection('core_write');
        $table = $this->_resource->getTableName($table);

        $db->query("TRUNCATE TABLE $table ");
        $db->query("ALTER TABLE $table DISABLE KEYS");
        $db->query("INSERT INTO $table " . $this->getIndexSelect());
        $db->query("ALTER TABLE $table ENABLE KEYS");

        return true;
    }

    /**
     * @return string
     */
    public function getIndexTable()
    {
        return 'amsorting_' . static::CODE;
    }

    /**
     * @param $collection
     * @param $currDir
     *
     * @return $this
     */
    public function apply($collection, $currDir)
    {
        $table = $this->getIndexTable();
        if ($table
            && $this->_scopeConfig->getValue(
                'amsorting/general/use_index',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        ) {
            $collection->joinField(
                static::CODE,      // alias
                $table,                // table
                static::CODE,      // field
                'id=entity_id',      // bind
                array('store_id' => $this->_storeManager->getStore()->getId()),
                // conditions
                'left'                 // join type
            );
        } else {
            $select = $collection->getSelect();
            $col = $select->getPart('columns');
            $col[] = array('', $this->getColumnSelect(), static::CODE);
            $select->setPart('columns', $col);
        }
        $collection->getSelect()->order(static::CODE . ' ' . $currDir);

        return $this;
    }
}
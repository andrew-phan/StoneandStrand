<?php

namespace Ss\Collection\Model\ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_attributeOptionTable;
    protected $_attributeOptionValueTable;
    protected $_attributeCollectionId;

    public function _construct()
    {
        $this->_init('ss_collection_collection', 'collection_id');
    }

    /**
     * Process post data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!is_null($object->getUrlKey())) {
            if (!$this->isValidCollectionUrlKey($object)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __('The collection URL key contains capital letters or disallowed symbols.')
                );
            }

            if ($this->isNumericCollectionUrlKey($object)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __('The collection URL key cannot be made of only numbers.')
                );
            }

            if ($this->checkExistUrlKey($object->getUrlKey(), $object->getId())) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __('The collection URL key has been exist.')
                );
            }
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->_saveOptionId($object);
        
        return parent::_afterSave($object);
    }

    /**
     * Delete Tags follow designer_id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function delete(\Magento\Framework\Model\AbstractModel $object)
    {
        $result = parent::delete($object);

        $this->_deleteOptionIds($object->getCollectionId());
        return $result;
    }

    /**
     * @todo to get table eav_attribute_option
     * @return type
     */
    public function getEavAttributeOptionTable()
    {
        if (!$this->_attributeOptionTable) {
            $this->_attributeOptionTable = $this->getTable('eav_attribute_option');
        }
        return $this->_attributeOptionTable;
    }

    /**
     * @todo to get table eav_attribute_option_value
     * @return type
     */
    public function getEavAttributeOptionValueTable()
    {
        if (!$this->_attributeOptionValueTable) {
            $this->_attributeOptionValueTable = $this->getTable('eav_attribute_option_value');
        }
        return $this->_attributeOptionValueTable;
    }

    /**
     * Retrieve product category identifiers
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getOptionIds($attributeId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
                $this->getEavAttributeOptionTable(), 'option'
            )->where(
            'attribute_id = ?', (int) $attributeId
        );

        return $connection->fetchCol($select);
    }

    /**
     * @todo To check exist option value from collection Name
     * @param type $optionId
     * @return type
     */
    public function checkExistOptionId($optionId,
        $attributeId,
        $collectionId = '')
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
                $this->getEavAttributeOptionValueTable() . ' AS option_value', 'option_value.option_id'
            )->join(
                ['option' => $this->getEavAttributeOptionTable()], '`option`.`option_id` = `option_value`.`option_id`', [
                ''
                ]
            )->where(
            '`option`.`attribute_id` = ?', $attributeId
        );

        if (!empty($optionId)) {
            $select->where(
                '`option_value`.`option_id` = ?', $optionId
            );
        }

        if (!empty($collectionId)) {
            $select->join(
                ['collection' => $this->getMainTable()], '`collection`.`option_id` = `option_value`.`option_id`', [
                ''
                ]
            )->where(
                '`collection`.`collection_id` = ?', $collectionId
            );
        }

        return $connection->fetchCol($select);
    }

    /**
     * @todo To get attribute ID
     * @return type
     */
    public function getAttributeId()
    {
        if (!$this->_attributeCollectionId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $attributeResource = $objectManager->create('\Magento\Eav\Model\ResourceModel\Entity\Attribute');
            $this->_attributeCollectionId = $attributeResource->getIdByCode('catalog_product', \Ss\Collection\Model\Collection::ATTRIBUTE_CODE);
        }
        return $this->_attributeCollectionId;
    }

    /**
     * @todo to save collection to option table 
     * @param type $object
     * @return \Ss\Collection\Model\ResourceModel\Collection
     */
    public function _saveOptionId($object)
    {
        $connection = $this->getConnection();
        $optionTable = $this->getEavAttributeOptionTable();
        $optionValueTable = $this->getEavAttributeOptionValueTable();
        $attributeId = $this->getAttributeId();        
        
        // Check if this collection_id & attribute_id still not exist.
        if (is_null($object->getOptionId()) || empty($object->getOptionId())) {
            $collectionId = $object->getCollectionId();
            
            // Add new option to table option.
            $dataOption = [
                'attribute_id' => $attributeId,
                'sort_order' => '1'
            ];

            $connection->insert($optionTable, $dataOption);
            $optionId = $connection->lastInsertId($optionTable);

            // Add new option value to table option value.
            $dataOptionValue = [
                'option_id' => $optionId,
                'store_id' => '0',
                'value' => $object->getName()
            ];

            $connection->insert($optionValueTable, $dataOptionValue);


            // Update option_id to table ss_collection_collection.
            $dataCollection = [
                'option_id' => $optionId,
            ];

            $connection->update($this->getMainTable(), $dataCollection, 'collection_id = ' . $collectionId);
        } else {
            $update = [
                'value' => $object->getName(),
            ];

            $connection->update($optionValueTable, $update, 'option_id = ' . $object->getOptionId());
        }
        return $this;
    }

    /**
     * @todo to delete option and option value in table
     * @param type $collectionId
     * @return boolean
     */
    public function _deleteOptionIds($collectionId)
    {
        if (!$collectionId) {
            return FALSE;
        }

        $connection = $this->getConnection();
        $attributeId = $this->getAttributeId();
        // Get list option value.
        $listOptionIds = $this->checkExistOptionId('', $attributeId, $collectionId);
        if (!empty($listOptionIds)) {
            $optionIdTemp = '';
            foreach ($listOptionIds as $optionId) {
                $optionIdTemp = $optionId;
                $where = ['option_id = ?' => (int) $optionId];

                // Delete item from table option value.
                $connection->delete($this->getEavAttributeOptionValueTable(), $where);
            }

            $where = ['attribute_id = ?' => (int) $attributeId,
                'option_id = ?' => $optionIdTemp];

            // Delete item from table option.
            $connection->delete($this->getEavAttributeOptionTable(), $where);
        }
    }

    /**
     *  Check whether post url key is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericCollectionUrlKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     *  Check whether post url key is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isValidCollectionUrlKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }

    /**
     * Retrieve load select with filter by url_key and activity
     *
     * @param string $url_key
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByUrlKeySelect($url_key)
    {
        $select = $this->getConnection()->select()->from(
                ['bp' => $this->getMainTable()]
            )->where(
            'bp.url_key = ?', $url_key
        );


        return $select;
    }

    /**
     * Check if post url key exists
     * return post id if post exists
     *
     * @param string $url_key
     * @return int
     */
    public function checkExistUrlKey($url_key,
        $collection_id = NULL)
    {
        $select = $this->_getLoadByUrlKeySelect($url_key);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('bp.collection_id')->limit(1);

        $firstRow = $this->getConnection()->fetchOne($select);
        if (!is_null($collection_id) && $firstRow && $firstRow == $collection_id) {
            return FALSE;
        }

        return $firstRow;
    }

}

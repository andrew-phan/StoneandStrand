<?php

namespace Ss\Designer\Model\ResourceModel;

/**
 * Class Resource Designer
 */
class Designer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_designerTagTable;
    protected $_prouctCollectionFactory;
    protected $_urlRewriteHelper;
    protected $_urlRewriteDesigner;
    protected $_attributeOptionTable;
    protected $_attributeOptionValueTable;
    protected $_attributeCollectionId;
    protected $_catalogProductVisibility;
    protected $_catalogConfig;

    public function _construct()
    {
        $this->_init('ss_designer_designer', 'designer_id');
    }

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $prouctCollectionFactory,
        \Ss\Designer\Helper\SsUrlRewriteProduct $urlRewriteHelper,
        \Ss\Designer\Helper\SsUrlRewriteDesigner $urlRewriteDesigner,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\Config $catalogConfig,
        $connectionName = null)
    {
        parent::__construct($context, $connectionName);
        $this->_prouctCollectionFactory = $prouctCollectionFactory;
        $this->_urlRewriteHelper = $urlRewriteHelper;
        $this->_urlRewriteDesigner = $urlRewriteDesigner;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_catalogConfig = $catalogConfig;
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
     * Process post data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!is_null($object->getUrlKey())) {
            if (!$this->isValidDesignerUrlKey($object)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __('The designer URL key contains capital letters or disallowed symbols.')
                );
            }

            if ($this->isNumericDesignerUrlKey($object)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __('The designer URL key cannot be made of only numbers.')
                );
            }

            if ($this->checkExistUrlKey($object->getUrlKey(), $object->getId())) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __('The designer URL key has been exist.')
                );
            }
        }


        return parent::_beforeSave($object);
    }

    /**
     * Designer tag table name getter
     *
     * @return string
     */
    public function getDesignerTagTable()
    {
        if (!$this->_designerTagTable) {
            $this->_designerTagTable = $this->getTable('ss_designer_designer_tag');
        }
        return $this->_designerTagTable;
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

        // Check if this designer assigned to product.
        $productCollections = $this->getProductCollections($object->getId());
        if ($productCollections->getSize() > 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
            __('This designer has assigned to product.')
            );
        }

        // Delete Option Ids.
        $this->_deleteOptionIds($object->getId());

        $result = parent::delete($object);

        $tagIds = $this->getTagIds($object->getId());
        $this->_deleteTags($object->getId(), $tagIds);


        // Delete Url designer
        $this->deleteUrlRewriteDesigner($object);

        return $result;
    }

    /**
     * Save data related with product
     *
     * @param \Magento\Framework\DataObject $product
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->_saveTags($object);

        // Check if url key change, then update url rewrite
        if ($object->getData(\Ss\Designer\Model\Designer::PARAM_CHANGE_URL_KEY) || $object->getData(\Ss\Designer\Model\Designer::PARAM_CHANGE_TYPE)) {
            // Update url rewrite for product.
            $this->updateUrlRewriteProduct($object);

            // Check is new
            if ($object->getIsNew()) {
                $this->addUrlRewriteDesigner($object);
            } else {
                $this->updateUrlRewriteDesigner($object);
            }
        }

        // Create/Update option id.
        $this->_saveOptionId($object);

        return parent::_afterSave($object);
    }

    public function _saveTags($object)
    {
        /**
         * If tag ids data is not declared we haven't do manipulations
         */
        if (!$object->hasParamTagIds()) {
            return $this;
        }
        $tagIds = $object->getParamTagIds();
        $oldTagIds = $this->getTagIds($object->getId());

        $object->setIsChangedTags(false);

        $insert = array_diff($tagIds, $oldTagIds);
        $delete = array_diff($oldTagIds, $tagIds);

        $connection = $this->getConnection();
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $tagId) {
                if (empty($tagId)) {
                    continue;
                }
                $data[] = [
                    'tag_id' => (int) $tagId,
                    'designer_id' => (int) $object->getId(),
                ];
            }
            if ($data) {
                $connection->insertMultiple($this->getDesignerTagTable(), $data);
            }
        }

        if (!empty($delete)) {
            $this->_deleteTags($object->getId(), $delete);
        }


        return $this;
    }

    /**
     * @todo To delete Tags from designer_id
     * @param type $designer_id
     * @param type $tagIds
     * @return boolean
     */
    protected function _deleteTags($designer_id,
        $tagIds)
    {
        if (is_null($tagIds) && empty($tagIds)) {
            return TRUE;
        }

        $connection = $this->getConnection();

        foreach ($tagIds as $tagId) {
            $where = ['designer_id = ?' => (int) $designer_id,
                'tag_id = ?' => (int) $tagId];

            $connection->delete($this->getDesignerTagTable(), $where);
        }

        return TRUE;
    }

    /**
     * Retrieve product category identifiers
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getTagIds($designer_id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
                $this->getDesignerTagTable(), 'tag_id'
            )->where(
            'designer_id = ?', (int) $designer_id
        );

        return $connection->fetchCol($select);
    }

    /**
     *  Check whether post url key is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericDesignerUrlKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     *  Check whether post url key is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isValidDesignerUrlKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }

    /**
     * Check if post url key exists
     * return post id if post exists
     *
     * @param string $url_key
     * @return int
     */
    public function checkExistUrlKey($url_key,
        $designer_id = NULL)
    {
        $select = $this->getConnection()->select()->from(
                ['bp' => $this->getMainTable()]
            )->where(
            'bp.url_key = ?', $url_key
        );
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('bp.designer_id')->limit(1);

        $firstRow = $this->getConnection()->fetchOne($select);
        if (!is_null($designer_id) && $firstRow && $firstRow == $designer_id) {
            return FALSE;
        }

        return $firstRow;
    }

    /**
     * @todo get Designer by name
     * @param type $name
     */
    public function getDesignerByName($name)
    {
        $select = $this->getConnection()->select()->from(
                ['bp' => $this->getMainTable()]
            )->where(
                'bp.name = ?', $name
            )
            ->columns('bp.designer_id')
            ->limit(1);
        ;

        $firstRow = $this->getConnection()->fetchOne($select);

        return $firstRow;
    }

    /**
     * @todo To get list product follow designer ID.
     */
    public function getProductCollections($designerId)
    {
        $attributes = $this->_catalogConfig->getProductAttributes();
        
        $productCollection = $this->_prouctCollectionFactory->create()
            ->addAttributeToFilter(\Ss\Designer\Model\Designer::ATTRIBUTE_CODE, array(
                'IN' => $designerId))
            ->addAttributeToSelect($attributes)
            ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        ;
        return $productCollection;
    }

    /**
     * @todo To update url rewrite for product
     * @param type $designer
     */
    private function updateUrlRewriteProduct($designer)
    {
        if ($designer->getDesignerId()) {
            $productCollections = $this->getProductCollections($designer->getDesignerId());            
            foreach ($productCollections as $product) {
                $this->_urlRewriteHelper->setProduct($product);
                $this->_urlRewriteHelper->setPrefixUrlDesigner($designer->getUrlPath(TRUE));
                $this->_urlRewriteHelper->setOldUrlDesigner($designer->getOldUrlKey());
                $this->_urlRewriteHelper->setIsEditDesigner(TRUE);
                $this->_urlRewriteHelper->setIsBackupUrl($designer->getData(\Ss\Designer\Model\Designer::PARAM_BACKUP_URL));
                $this->_urlRewriteHelper->generateUrlRewrite();
            }
        }
    }

    /**
     * @todo To delete all url rewrite of product
     * @param type $designerId
     */
    public function deleteUrlRewriteProduct($designerId)
    {
        $productCollections = $this->getProductCollections($designerId);
        foreach ($productCollections as $product) {
            $this->_urlRewriteHelper->deleteUrlProduct($product);
        }
    }

    /**
     * @todo To prepare data for url rewrite designer
     * @param type $designer
     */
    private function prepareUrlRewritedDesigner($designer)
    {
        $this->_urlRewriteDesigner->setRequestPath($designer->getUrlPath(TRUE));
        $this->_urlRewriteDesigner->setTargetPath($designer->getTargetPath());
        $this->_urlRewriteDesigner->setIsBackupUrl($designer->getData(\Ss\Designer\Model\Designer::PARAM_BACKUP_URL));
        $this->_urlRewriteDesigner->setOldUrlDesigner($designer->getOldUrlKey());
    }

    /**
     * @todo to add new url rewrite for designer
     * @param type $designer
     */
    private function addUrlRewriteDesigner($designer)
    {
        $this->prepareUrlRewritedDesigner($designer);
        $this->_urlRewriteDesigner->createUrlRewrite();
    }

    /**
     * @todo to Update Url Rewrite Designer
     * @param type $designer
     */
    public function updateUrlRewriteDesigner($designer)
    {
        $this->prepareUrlRewritedDesigner($designer);
        $this->_urlRewriteDesigner->updateUrlRewrite();
    }

    /**
     * @todo To delete url rewrite designer
     * @param type $designer
     */
    private function deleteUrlRewriteDesigner($designer)
    {
        $this->prepareUrlRewritedDesigner($designer);
        $this->_urlRewriteDesigner->deleteUrlRewrite();
    }

    /**
     * @todo To check exist option value from option_id
     * @param type $optionId
     * @return type
     */
    public function checkExistOptionId($optionId,
        $attributeId,
        $designerId = '')
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

        if (!empty($designerId)) {
            $select->join(
                ['designer' => $this->getMainTable()], '`designer`.`option_id` = `option_value`.`option_id`', [
                ''
                ]
            )->where(
                '`designer`.`designer_id` = ?', $designerId
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
            $this->_attributeCollectionId = $attributeResource->getIdByCode('catalog_product', \Ss\Designer\Model\Designer::ATTRIBUTE_CODE);
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
            $designerId = $object->getDesignerId();

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

            $connection->update($this->getMainTable(), $dataCollection, 'designer_id = ' . $designerId);
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
     * @param type $designerId
     * @return boolean
     */
    public function _deleteOptionIds($designerId)
    {
        if (!$designerId) {
            return FALSE;
        }

        $connection = $this->getConnection();
        $attributeId = $this->getAttributeId();
        // Get list option value.
        $listOptionIds = $this->checkExistOptionId('', $attributeId, $designerId);
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
     * @todo To Get Deisnger Id by option id
     * @param type $optionId
     * @return type
     */
    public function getDesignerIdByOptionId($optionId)
    {
        if (!$optionId) {
            return '';
        }

        $connection = $this->getConnection();

        $select = $connection->select()->from(
                $this->getMainTable() . ' AS designer', 'designer.designer_id'
            )->where(
            '`designer`.`option_id` = ?', $optionId
        );

        return $connection->fetchCol($select);
    }

}

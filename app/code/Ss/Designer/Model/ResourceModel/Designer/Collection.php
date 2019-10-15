<?php

namespace Ss\Designer\Model\ResourceModel\Designer;

/**
 * Subscription Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_designerTagTable;
    protected $_designerTypeTable;

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ss\Designer\Model\Designer', 'Ss\Designer\Model\ResourceModel\Designer');
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

    public function getDesignerTypeTable()
    {
        if (!$this->_designerTypeTable) {
            $this->_designerTypeTable = $this->getTable('ss_designer_type');
        }
        return $this->_designerTypeTable;
    }

    /**
     * @todo to Add join with table type.
     * @return \Ss\Designer\Model\ResourceModel\Tags\Collection
     */
    public function addJoinDesignerTypeTable()
    {
        $this->getSelect()
            ->join(array('type' => $this->getDesignerTypeTable()), 'type.type_id = main_table.type_id', array('type_name' => 'type.name')
        );
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field,
        $condition = null)
    {
        if (in_array($field, ['filter_grid_tag_ids', 'type_id']) && count($condition) == 1 && isset($condition["eq"]) && $condition["eq"] == 0) {
            return FALSE;
        }
        if ($field === 'filter_grid_tag_ids') {
            return $this->addAttributeToFilter('tag_id', $condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add attribute to filter
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addAttributeToFilter($attribute,
        $condition = null)
    {
        if ($attribute == 'tag_id') {
            $this->getSelect()->join(array(
                'tags' => $this->getDesignerTagTable()), 'tags.designer_id = main_table.designer_id', array(
                '*'));

            $this->addFieldToFilter('tags.' . $attribute, $condition);
        } else {
            $this->addFieldToFilter('main_table.' . $attribute, $condition);
        }

        return $this;
    }

    public function getAttributeId()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attributeResource = $objectManager->create('\Magento\Eav\Model\ResourceModel\Entity\Attribute');
        return $attributeResource->getIdByCode('catalog_product', \Ss\Designer\Model\Designer::ATTRIBUTE_CODE);
    }

    public function addCountToProducts($productCollection)
    {
        $attributeId = $this->getAttributeId();
        // clone select from collection with filters
        $select = clone $productCollection->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $connection = $this->getConnection();


        $select->join(
                ['count_table' => $this->getTable('catalog_product_entity_int')], 'count_table.entity_id = e.entity_id', [
                'count_table.value',
                'product_count' => new \Zend_Db_Expr('COUNT(DISTINCT count_table.entity_id)')
                ]
            )
            ->where(
                'count_table.attribute_id = ?', $attributeId
            )
            ->group(
                'count_table.value'
        );

        return $connection->fetchPairs($select);
    }

    /**
     * @todo To group designer by type
     * @param type $listDesignerIds
     * @return type
     */
    public function groupDesignerByType($listDesignerIds)
    {
        $this->addJoinDesignerTypeTable();
        return $this->addFieldToFilter('main_table.designer_id', ['IN' => $listDesignerIds])
            ->setOrder('main_table.name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
    }

}

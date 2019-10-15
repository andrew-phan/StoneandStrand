<?php

namespace Ss\Designer\Model\ResourceModel\Tags;

/**
 * Subscription Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_optionsTags;
    protected $_designerTagTable;
    protected $_designerTypeTable;

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ss\Designer\Model\Tags', 'Ss\Designer\Model\ResourceModel\Tags');
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

    public function addJoinDesignerTagTable()
    {
        $this->getSelect()->join(array('tags' => $this->getDesignerTagTable()), 'tags.tag_id = main_table.tag_id', array('*'));
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
        if ($field === 'designer_id') {
            $this->addJoinDesignerTagTable();
            $this->getSelect()->group('tags.tag_id');
            return $this->addFieldToFilter('tags.' . $field, $condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @todo to get list option tag.
     * @param type $renderLableValue
     * @return type
     */
    public function getOptionsTags($renderLableValue = TRUE)
    {
        if (is_null($this->_optionsTags)) {
            $this->_optionsTags[] = ($renderLableValue) ? ['label' => 'All Tags', 'value' => ''] : 'All Tags';
            foreach ($this as $item) {
                if ($renderLableValue) {
                    $this->_optionsTags[] = ['label' => $item->getName(), 'value' => $item->getTagId()];
                } else {
                    $this->_optionsTags[$item->getTagId()] = $item->getName();
                }
            }
        }

        return $this->_optionsTags;
    }

}

<?php

namespace Ss\Designer\Model\ResourceModel\Type;

/**
 * Type Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_optionsTypes;

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ss\Designer\Model\Type', 'Ss\Designer\Model\ResourceModel\Type');
    }

    public function getOptionsTypes($renderLableValue = TRUE)
    {
        if (is_null($this->_optionsTypes)) {
            $this->_optionsTypes[] = ($renderLableValue) ? ['label' => 'All Types', 'value' => ''] : 'All Types';
            foreach ($this as $item) {
                if ($renderLableValue) {
                    $this->_optionsTypes[] = ['label' => $item->getName(), 'value' => $item->getTypeId()];
                } else {
                    $this->_optionsTypes[$item->getTypeId()] = $item->getName();
                }
            }
        }

        return $this->_optionsTypes;
    }

}

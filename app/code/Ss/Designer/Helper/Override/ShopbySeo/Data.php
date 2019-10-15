<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Ss\Designer\Helper\Override\ShopbySeo;

/**
 * Helper Data
 */
class Data extends \Amasty\ShopbySeo\Helper\Data
{

    protected $_designerHelper;

    public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Amasty\Shopby\Model\ResourceModel\FilterSetting\CollectionFactory $settingCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $optionCollectionFactory,
        \Magento\Catalog\Model\Product\Url $productUrl,
        \Ss\Designer\Helper\Data $designerHelper
    )
    {
        parent::__construct($context, $settingCollectionFactory, $optionCollectionFactory, $productUrl);
        $this->_designerHelper = $designerHelper;
    }

    /**
     * @todo To get all option seo data
     * @return type
     */
    public function getOptionsSeoData()
    {
        if (is_null($this->optionsSeoData)) {
            $seoAttributeCodes = $this->getSeoSignificantAttributeCodes();

            $collection = $this->optionCollectionFactory->create();
            $collection->join(['a' => 'eav_attribute'], 'a.attribute_id = main_table.attribute_id', ['attribute_code']);
            $collection->addFieldToFilter('attribute_code', ['in' => $seoAttributeCodes]);
            $collection->setStoreFilter();
            $select = $collection->getSelect();

            $statement = $select->query();
            $rows = $statement->fetchAll();
            $this->optionsSeoData = [];
            $aliasHash = [];
            foreach ($rows as $row) {
                $alias = $this->buildUniqueAlias($row['value'], $aliasHash);
                $optionId = $row['option_id'];
                if ($row['attribute_code'] == \Ss\Designer\Model\Designer::ATTRIBUTE_CODE) {
                    $designerId = $this->_designerHelper->getDesignerIdByOptionId($optionId);
                    if (!empty($designerId)) {
                        $optionId = \Ss\Designer\Model\Designer::ATTRIBUTE_CODE . array_shift($designerId);
                    }
                }
                $this->optionsSeoData[$optionId] = [
                    'alias' => $alias,
                    'attribute_code' => $row['attribute_code'],
                ];
                $aliasHash[$alias] = $optionId;
            }
        }

        return $this->optionsSeoData;
    }

}

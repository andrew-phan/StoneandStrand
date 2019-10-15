<?php

/**
 * Diamond helper
 */

namespace Ss\Theme\Helper;

/**
 * Diamond helper
 */
class Attributes extends \Magento\Framework\App\Helper\AbstractHelper
{

    const NORMAL_SIZE = 'size';     // Attribute Size product
    const TYPE_GROUP_PRODUCT = 'grouped';   // Product type group
    const SORT_ORDER_NAME = "name";     // Sort order by name
    const SORT_ORDER_PRICE = "price";     // Sort order by price
    const SORT_ORDER_BESTSELLER = "bestsellers";     // Sort order by bestseller
    const SORT_ORDER_NEWEST = "newest";     // Sort order by newest
    const SORT_DIRECTION_ASC = "asc";     // Sort direction asc
    const SORT_DIRECTION_DESC = "desc";     // Sort direction asc
    const URL_KEY = "url_key";     // attribute url_key product
    const ENTITY_ID = "entity_id";     // attribute entity_id product
    const CUSTOMER_HOW_YOU_HEAR = "how_did_you_hear";   // attribute customer
    const CUSTOMER_FORM_TYPE = "customer";  //  get from tatle eav_entity_type
    const PRODUCT_TYPE = "type_id";    // attribute product type
    const PRODUCT_TYPE_VIRTUAL = "virtual"; // value product type virtual

    protected $_eavConfig;

    /**
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Magento\Eav\Model\Config $eavConfig)
    {
        parent::__construct($context);

        $this->_eavConfig = $eavConfig;
    }

    /**
     * @todo to get all option attribute customer how_did_you_hear
     */
    public function getOptionsCustomerHowYouHear()
    {
        $attribute = $this->_eavConfig->getAttribute(static::CUSTOMER_FORM_TYPE, static::CUSTOMER_HOW_YOU_HEAR);
        $options = $attribute->getSource()->getAllOptions();
        $results = [];
        foreach ($options as $option) {
            if (!empty($option["label"])) {
                $results[] = $option["label"];
            }
        }

        return $results;
    }

}

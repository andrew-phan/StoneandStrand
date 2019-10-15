<?php

/**
 * Diamond helper
 */

namespace Ss\Theme\Helper;

/**
 * Diamond helper
 */
class Diamond extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_requestParam;

    const ATTRIBUTE_SHAPE = 'diamond_shape';
    const ATTRIBUTE_SIZE = 'diamond_size';
    const ATTRIBUTE_STONE_COLOR = 'diamond_stone_color';
    const ATTRIBUTE_METAL_COLOR = 'diamond_metal_color';
    const ATTRIBUTE_DELIMITER = ',';

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository $_productAttributeRepository
     */
    protected $_productAttributeRepository;
    protected $_swatchCollectionFactory;
    protected $_listOptionLabels;
    protected $_request;

    /**
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
     * @param \Ss\Theme\Helper\Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory $swatchCollectionFactory
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory $swatchCollectionFactory,
        \Magento\Framework\App\Request\Http $request
    )
    {
        parent::__construct($context);
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_swatchCollectionFactory = $swatchCollectionFactory;
        $this->_request = $request;
    }

    /**
     * @todo To get list option Ids from attribute code.
     * @param type $attributeCode
     * @return type
     */
    public function getOptionIds($attributeCode)
    {
        $results = [];
        $listOptions = $this->_productAttributeRepository->get($attributeCode)->getOptions();
        foreach ($listOptions as $option) {
            $results[] = $option->getValue();
            $this->_listOptionLabels[$option->getValue()] = $option->getLabel();
        }

        return $results;
    }

    /**
     * @todo to get list swatch otion from attribute code
     * @param type $attributeCode
     * @return type
     */
    public function getListSwatchOption($attributeCode)
    {
        $optionIds = $this->getOptionIds($attributeCode);
        $collection = [];
        if (!empty($optionIds)) {
            $collection = $this->_swatchCollectionFactory->create()->addFilterByOptionsIds($optionIds);
        }

        return $collection;
    }

    /**
     * @todo to get list option of attribute diamond_shape
     * @return type
     */
    public function getListAttributeShape()
    {
        return $this->getListSwatchOption(static::ATTRIBUTE_SHAPE);
    }

    /**
     * @todo to get list option of attribute diamond_size
     * @return type
     */
    public function getListAttributeSize()
    {
        return $this->getListSwatchOption(static::ATTRIBUTE_SIZE);
    }

    /**
     * @todo to get list option of attribute diamond_stone_color
     * @return type
     */
    public function getListAttributeStoneColor()
    {
        return $this->getListSwatchOption(static::ATTRIBUTE_STONE_COLOR);
    }

    /**
     * @todo to get list option of attribute diamond_metal_color
     * @return type
     */
    public function getListAttributeMetalColor()
    {
        return $this->getListSwatchOption(static::ATTRIBUTE_METAL_COLOR);
    }

    /**
     * @todo to get all attribute of diamond
     * @return type
     */
    public function getListDiamondAttribute()
    {
        $results = [];
        $results[static::ATTRIBUTE_SHAPE] = [
            'title' => __('choose shape'),
            'class' => 'choose-shape',
            'options' => $this->getListAttributeShape()
        ];

        $results['color'] = [
            'title' => __('choose color'),
            'class' => 'choose-color',
            'options' => [
                static::ATTRIBUTE_METAL_COLOR => [
                    'title' => __('Metal color'),
                    'items' => $this->getListAttributeMetalColor(),
                ],
                static::ATTRIBUTE_STONE_COLOR => [
                    'title' => __('Stone color'),
                    'items' => $this->getListAttributeStoneColor()
                ]
            ]
        ];

        $results[static::ATTRIBUTE_SIZE] = [
            'title' => __('choose size'),
            'class' => 'choose-size',
            'options' => $this->getListAttributeSize()
        ];

        return $results;
    }

    /**
     * @todo To get list option label
     * @return type
     */
    public function getListOptionLabels()
    {
        return $this->_listOptionLabels;
    }

    /**
     * @todo To get param attribute code
     * @param type $attributeCode
     * @return type
     */
    public function getAttributeParam($attributeCode,
        $parseArray = FALSE)
    {
        $value = $this->_request->getParam($attributeCode, '');
        if ($parseArray && !empty($value)) {
            return explode(static::ATTRIBUTE_DELIMITER, $value);
        }
        return $value;
    }

    /**
     * @todo Get filter item url for attribute code
     * @param type $attributeCode
     * @param type $attributeId
     * @return type
     */
    public function getFilterUrl($attributeCode,
        $attributeId)
    {
        $defaultParam = $this->getAttributeParam($attributeCode, TRUE);
        if (!empty($defaultParam)) {
            // Check if this value exist in url, Then remove it.
            if (!empty($key = array_keys($defaultParam, $attributeId))) {
                unset($defaultParam[$key[0]]);
            } else {
                // Add multiple param attribute.
                $defaultParam[] = $attributeId;
            }

            $defaultParam = implode(',', $defaultParam);
        } else {
            $defaultParam = $attributeId;
        }

        // Get all query from request.
        $query = $this->parseParamRequestToQuery($attributeCode);

        // Add or remove this param to url.
        $query[$attributeCode] = (!empty($defaultParam)) ? $defaultParam : null;

        return $this->_getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * @todo To parse param from request to query param
     * @param type $excludeAttrCode
     * @return type
     */
    public function parseParamRequestToQuery()
    {
        if ($this->_requestParam) {
            return $this->_requestParam;
        }

        $query = [];
        $listAttr = [static::ATTRIBUTE_SHAPE,
            static::ATTRIBUTE_METAL_COLOR,
            static::ATTRIBUTE_STONE_COLOR,
            static::ATTRIBUTE_SIZE];

        foreach ($listAttr as $attr) {
            $value = $this->getAttributeParam($attr);
            if ($value) {
                $query[$attr] = $value;
            }
        }

        $this->_requestParam = $query;
        return $query;
    }

}

<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Ss\Sales\Block\Order\Item\Renderer;

/**
 * Order item render block
 */
class DefaultRenderer extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
{
    const ATTRIBUTE_CODE_SIZE = 'size'; // code of product size attribute
    const ATTRIBUTE_CODE_DIAMOND_SIZE = 'diamond_size'; // code of product diamond size attribute
    const KEY_INFO_PRODUCT_ITEM = 'info_buyRequest'; // key get info product by item.
    const KEY_PRODUCT_CHOOSED = 'selected_configurable_option'; // key get product had been choose.
    const OPTION_DEFAULT = 'default'; // key option default
    const OPTION_EXTENSION = 'extension'; // key option extension
    
    protected $_itemHelper;
    protected $_designerHelper;
    protected $productRepository;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Sales\Helper\Item $itemHelper
     * @param \Ss\Designer\Helper\Data $designerHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ss\Sales\Helper\Item $itemHelper,
        \Ss\Designer\Helper\Data $designerHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->_itemHelper = $itemHelper;
        $this->productRepository = $productRepository;
        $this->_designerHelper = $designerHelper;
        parent::__construct($context, $string, $productOptionFactory, $data);
    }

    /**
     * Get source image url
     * @return string
     */
    public function getSrcMediaImage()
    {
        return $this->_designerHelper->getSrcMediaImage();

    }
    /**
     * Get item helper
     * @return \Ss\Sales\Helper\Item
     */
    public function getItemHelper()
    {
        return $this->_itemHelper;
    }
    
    /**
     * Get designer helper
     * @return \Ss\Designer\Helper\Data
     */
    public function getDesignerHelper()
    {
        return $this->_designerHelper;
    }
    
    /**
     * Get product factory
     * @return type
     */
    public function getProductRepository()
    {
        return $this->productRepository;
    }
    
    /**
     * Get product id
     * 
     * @return int
     */
    public function getProductId()
    {
        $infoBuyRequest = $this->getItem()->getProductOptionByCode(static::KEY_INFO_PRODUCT_ITEM);
        $productId = empty($infoBuyRequest[static::KEY_PRODUCT_CHOOSED]) ? $this->getItem()->getProductId() : $infoBuyRequest[static::KEY_PRODUCT_CHOOSED];

        return $productId;
    }

    /**
     * @return array
     */
    public function getItemOptions()
    {
        $default = [];
        $extension = [];
        $options = $this->getOrderItem()->getProductOptions();
        
        if ($options) {
            if (isset($options['options'])) {
                $extension = array_merge($extension, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $default = array_merge($default, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $default = array_merge($default, $options['attributes_info']);
            }
        }
        
        $result = array_filter([static::OPTION_DEFAULT => $default, static::OPTION_EXTENSION => $extension]);
        
        return $result;
    }
}

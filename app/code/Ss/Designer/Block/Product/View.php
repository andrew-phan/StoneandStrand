<?php

namespace Ss\Designer\Block\Product;

/**
 * Product view
 */
class View extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Ashsmith\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    protected $_designerHelper;

    /**
     * @var Product
     */
    protected $_product = null;
    protected $_coreRegistry = null;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Designer\Model\DesignerFactory $designerFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
        \Ss\Designer\Helper\Data $designerHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_designerHelper = $designerHelper;
        $this->_coreRegistry = $registry;
    }

    public function getDesignerHelper()
    {
        return $this->_designerHelper;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    /**
     * @todo To get current designer
     * @return boolean
     */
    public function getDesigner()
    {
        $product = $this->getProduct();
        if (!$product) {
            return FALSE;
        }

        $result = FALSE;
        $designerId = $product->getSsDesigner();
        if (!empty($designerId)) {
            $result = $this->_designerHelper->getDesignerById($designerId);
        }
        return $result;
    }

}

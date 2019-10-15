<?php

namespace Ss\Designer\Block\Adminhtml\Designer\Helper\Renderer;

class ProductImage extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * Store manager.
     *
     */
    protected $_storeManager;

    /**
     * banner factory.
     *
     */
    protected $_designerFactory;

    /**
     * 
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ss\Designer\Model\DesignerFactory $designerFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ss\Designer\Model\DesignerFactory $designerFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_designerFactory = $designerFactory;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $designer = $this->_designerFactory->create()->load($row->getId());
        $srcImage = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . $designer->getImageProduct();

        return '<image width="150" height="50" src ="' . $srcImage . '" alt="' . $designer->getImageProduct() . '" >';
    }

}

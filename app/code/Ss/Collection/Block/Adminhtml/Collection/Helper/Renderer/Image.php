<?php

namespace Ss\Collection\Block\Adminhtml\Collection\Helper\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
    protected $_collectionFactory;

    /**
     * 
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ss\Collection\Model\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Ss\Collection\Model\CollectionFactory $collectionFactory, array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
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
        $collection = $this->_collectionFactory->create()->load($row->getId());
        $srcImage = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . $collection->getImage();

        return '<image width="150" height="50" src ="' . $srcImage . '" alt="' . $collection->getImage() . '" >';
    }

}

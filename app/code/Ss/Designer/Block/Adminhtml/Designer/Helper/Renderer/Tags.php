<?php

namespace Ss\Designer\Block\Adminhtml\Designer\Helper\Renderer;

class Tags extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
    protected $_backendUrl;

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
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_designerFactory = $designerFactory;
        $this->_backendUrl = $backendUrl;
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
        $listTags = $this->_designerFactory->create()->getTagNames($row->getId());
        $html = '';
        foreach ($listTags as $tagId => $tagName) {
            $url = $this->_backendUrl->getUrl('*/tags/edit', ['tag_id' => $tagId]);
            $html .= '<a title="' . $tagName . '" href="' . $url . '" target="_blank">' . $tagName . "</a>, ";
        }

        if (!empty($html)) {
            $html = substr($html, 0, -2);
        }

        return $html;
    }

}

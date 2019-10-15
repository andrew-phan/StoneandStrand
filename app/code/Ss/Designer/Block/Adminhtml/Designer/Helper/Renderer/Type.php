<?php

namespace Ss\Designer\Block\Adminhtml\Designer\Helper\Renderer;

class Type extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * type factory.
     *
     */
    protected $_typeFactory;
    protected $_backendUrl;

    /**
     * 
     * @param \Magento\Backend\Block\Context $context
     * @param \Ss\Designer\Model\TypeFactory $typeFactory
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Context $context,
        \Ss\Designer\Model\TypeFactory $typeFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_typeFactory = $typeFactory;
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
        $typeId = $row->getTypeId();
        $typeModel = $this->_typeFactory->create()->load($typeId);
        $url = $this->_backendUrl->getUrl('*/type/edit', ['type_id' => $typeId]);

        return '<a title="' . $typeModel->getName() . '" href="' . $url . '" target="_blank">' . $typeModel->getName() . "</a>";
    }

}

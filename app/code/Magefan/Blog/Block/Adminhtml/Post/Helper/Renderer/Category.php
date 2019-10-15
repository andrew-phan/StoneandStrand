<?php

namespace Magefan\Blog\Block\Adminhtml\Post\Helper\Renderer;

class Category extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    /**
     * 
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $categoryNames = $row->getCategoryName();
        $html = '';
        if ($categoryNames) {
            foreach ($categoryNames as $cateName) {
                $html .= $cateName . ', ';
            }
            $html = substr($html, 0, -2);
        }
        return $html;
    }

}

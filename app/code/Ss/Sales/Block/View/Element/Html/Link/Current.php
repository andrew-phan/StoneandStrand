<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Sales\Block\View\Element\Html\Link;

/**
 * Block representing link with two possible states.
 * "Current" state means link leads to URL equivalent to URL of currently displayed page.
 *
 * @method string                          getLabel()
 * @method string                          getPath()
 * @method string                          getTitle()
 * @method null|array                      getAttributes()
 * @method null|bool                       getCurrent()
 * @method \Magento\Framework\View\Element\Html\Link\Current setCurrent(bool $value)
 */
class Current extends \Magento\Framework\View\Element\Html\Link\Current
{

    /**
     * Default path
     *
     * @var \Magento\Framework\App\DefaultPathInterface
     */
    protected $_defaultPath;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\App\DefaultPathInterface $defaultPath, array $data = []
    )
    {
        parent::__construct($context, $defaultPath, $data);
        $this->_defaultPath = $defaultPath;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $html = '<li class="item"><a href="' . $this->escapeHtml($this->getHref()) . '"';
        $html .= $this->getTitle() ? ' title="' . $this->escapeHtml((string) new \Magento\Framework\Phrase($this->getTitle())) . '"' : '';
        $html .= $this->getAttributesHtml() . '>';

        $html .= $this->escapeHtml((string) new \Magento\Framework\Phrase($this->getLabel()));

        $html .= '</a>';

        $html .='<div class="tinymce-editor">';
        $html .='<p>'.$this->getDescription().'</p>';
        $html .='</div>';
        $html .='</li>';


        return $html;
    }

    /**
     * Generate attributes' HTML code
     *
     * @return string
     */
    private function getAttributesHtml()
    {
        $attributesHtml = '';
        $attributes = $this->getAttributes();
        if ($attributes) {
            foreach ($attributes as $attribute => $value) {
                $attributesHtml .= ' ' . $attribute . '="' . $this->escapeHtml($value) . '"';
            }
        }

        return $attributesHtml;
    }

}

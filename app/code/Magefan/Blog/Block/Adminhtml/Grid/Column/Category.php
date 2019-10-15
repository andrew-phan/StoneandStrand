<?php

/**
 * Copyright © 2015 Ihor Vansach (ihor@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Block\Adminhtml\Grid\Column;

/**
 * Admin blog grid statuses 
 */
class Category extends \Magento\Backend\Block\Widget\Grid\Column {

    protected $_optionCategories;
    protected $_objectManager;
    protected $_categoryFactory;
    protected $_backendUrl;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magefan\Blog\Model\Category $categoryFatory, \Magento\Backend\Model\UrlInterface $backendUrl, array $data = []) {
        parent::__construct($context, $data);
        $this->_categoryFactory = $categoryFatory;
        $this->_backendUrl = $backendUrl;
    }

    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback() {
        return [$this, 'decorateStatus'];
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport) {
        $html = '';
        if (!is_null($row->getCategories())) {
            foreach ($row->getCategories() as $categoryId) {
                $category = $this->_categoryFactory->load($categoryId);

                $url = $this->_backendUrl->getUrl('*/category/edit', ['id' => $categoryId]);
                $html .= '<a title="' . $category->getTitle() . '" href="' . $url . '" target="_blank">' . $category->getTitle() . "</a>, ";
            }

            if (!empty($html)) {
                $html = substr($html, 0, -2);
            }
        }

        return $html;
    }

    public function setOptions() {
        $this->_optionCategories = $this->_categoryFactory->getListOptionCategory();
        return $this->_optionCategories;
    }

    public function getOptions() {
        if (is_null($this->_optionCategories)) {
            $this->setOptions();
        }
        return $this->_optionCategories;
    }

    public function hasOptions() {
        return true;
    }

}

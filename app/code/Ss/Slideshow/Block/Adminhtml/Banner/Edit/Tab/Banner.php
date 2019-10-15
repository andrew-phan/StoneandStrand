<?php

/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Bannerslider
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Ss\Slideshow\Block\Adminhtml\Banner\Edit\Tab;

class Banner extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;

    /**
     * value collection factory.
     *
     * @var \Magestore\Bannerslider\Model\ResourceModel\Value\CollectionFactory
     */
    protected $_valueCollectionFactory;

    /**
     * slider factory.
     *
     * @var \Magestore\Bannerslider\Model\SliderFactory
     */
    protected $_sliderFactory;

    /**
     * @var \Magestore\Bannerslider\Model\Banner
     */
    protected $_banner;
    protected $_wysiwygConfig;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\DataObjectFactory $objectFactory
     * @param \Ss\Slideshow\Model\Banner $banner
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\DataObjectFactory $objectFactory, \Ss\Slideshow\Model\Banner $banner, \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, array $data = []
    )
    {
        $this->_objectFactory = $objectFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_banner = $banner;
        parent::__construct($context, $registry, $formFactory, $data);

    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $dataObj = $this->_objectFactory->create(
            ['data' => array()]
        );
        $model = $this->_coreRegistry->registry('banner');

        if ($sliderId = $this->getRequest()->getParam('current_slider_id')) {
            $model->setSliderId($sliderId);
        }

        $dataObj->addData($model->getData());

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('banner');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Banner Information')]);

        if ($model->getId()) {
            $fieldset->addField('banner_id', 'hidden', ['name' => 'banner_id']);
        }
        $options = ['homepage',
            'diamond'];
        $elements = [];
        $elements['name'] = $fieldset->addField(
            'name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true,
            ]
        );

        $elements['image'] = $fieldset->addField(
            'image', 'image', [
            'title' => __('Banner Image'),
            'label' => __('Banner Image'),
            'name' => 'image',
            'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );

        $elements['type'] = $fieldset->addField(
            'banner_type', 'select', [
            'label' => __('Type'),
            'title' => __('Type'),
            'name' => 'banner_type',
            'values' => $this->getValuesArray($options)
            ]
        );

        $elements['description'] = $fieldset->addField(
            'description', 'editor', [
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
            'style' => 'height:15em',
            'required' => false,
            'config' => $wysiwygConfig
            ]
        );

        $elements['target'] = $fieldset->addField(
            'target_url', 'text', [
            'label' => __('Target Url'),
            'title' => __('Target Url'),
            'name' => 'target_url',
            ]
        );

        $form->addValues($dataObj->getData());
        $this->setForm($form);

        return parent::_prepareForm();

    }

    /**
     * @return mixed
     */
    public function getBanner()
    {
        return $this->_coreRegistry->registry('banner');

    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getBanner()->getId() ? __("Edit Banner '%1'", $this->escapeHtml($this->getBanner()->getName())) : __('New Banner');

    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Banner Information');

    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Banner Information');

    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;

    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;

    }

    public function getValuesArray($option)
    {
        $valuesArr = array();
        $valuesArr["homepage"] = '-- Please Select --';

        foreach ($option as $value) {
            $valuesArr[$value] = $value;
        }
        return $valuesArr;

    }
}

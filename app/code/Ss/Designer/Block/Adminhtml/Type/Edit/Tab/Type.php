<?php

/**
 * Magestore
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

namespace Ss\Designer\Block\Adminhtml\Type\Edit\Tab;

/**
 * Banner Edit tab.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Type extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
    protected $_type;
    protected $_wysiwygConfig;

    /**
     * constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                        $context
     * @param \Magento\Framework\Registry                                    $registry
     * @param \Magento\Framework\Data\FormFactory                            $formFactory
     * @param \Magento\Framework\DataObjectFactory                               $objectFactory
     * @param \Magestore\Bannerslider\Model\Banner                           $banner
     * @param \Magestore\Bannerslider\Model\ResourceModel\Value\CollectionFactory $valueCollectionFactory
     * @param \Magestore\Bannerslider\Model\SliderFactory                    $sliderFactory
     * @param array                                                          $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Ss\Designer\Model\Type $type,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_objectFactory = $objectFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_type = $type;
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
        $model = $this->getDesignerType();

        if ($typeId = $this->getRequest()->getParam('current_type_id')) {
            $model->setSliderId($typeId);
        }

        $dataObj->addData($model->getData());

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('type');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Type Information')]);

        if ($model->getId()) {
            $fieldset->addField('type_id', 'hidden', ['name' => 'type_id']);
        }

        $elements = [];
        $elements['name'] = $fieldset->addField(
            'name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true,
            ]
        );

        $elements['is_vintage'] = $fieldset->addField(
            'is_vintage', 'select', [
            'name' => 'is_vintage',
            'label' => __('Vintage template'),
            'title' => __('Vintage template'),
            'required' => true,
            'values' => \Ss\Designer\Model\Type::STATUS_OPTION
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


        $form->addValues($dataObj->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getDesignerType()
    {
        return $this->_coreRegistry->registry('designer_type');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getDesignerType()->getId() ? __("Edit Type '%1'", $this->escapeHtml($this->getDesignerType()->getName())) : __('New Type');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Type Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Type Information');
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

}

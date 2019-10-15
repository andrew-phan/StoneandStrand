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
 */

namespace Ss\Collection\Block\Adminhtml\Collection\Edit\Tab;

class Collection extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;


    /**
     * @var \Magestore\Bannerslider\Model\Banner
     */
    protected $_wysiwygConfig;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\DataObjectFactory $objectFactory
     * @param \Ss\Collection\Model\Collection $collection
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\DataObjectFactory $objectFactory, \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, array $data = []
    )
    {
        $this->_objectFactory = $objectFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
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
        $model = $this->getCollection();

        if ($collectionId = $this->getRequest()->getParam('current_collection_id')) {
            $model->setSliderId($collectionId);
        }

        $dataObj->addData($model->getData());


        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('collection');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Collection Information')]);

        if ($model->getId()) {
            $fieldset->addField('collection_id', 'hidden', ['name' => 'collection_id']);
            $fieldset->addField('option_id', 'hidden', ['name' => 'option_id']);
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


        $elements['url_key'] = $fieldset->addField(
            'url_key', 'text', [
            'name' => 'url_key',
            'label' => __('Url key'),
            'title' => __('Url key'),
            'required' => true
            ]
        );

        $elements['url_menu'] = $fieldset->addField(
            'url_menu', 'text', [
            'name' => 'url_menu',
            'label' => __('Url Menu'),
            'title' => __('Url Menu'),
            'required' => false
            ]
        );

        $elements['is_active'] = $fieldset->addField('is_active', 'select', ['label' => __('Status'),'title' => __('Designer Status'),'name' => 'is_active','required' => false,'options' => \Ss\Designer\Model\Designer::ACTIVE_OPTION]);
        $elements['image'] = $fieldset->addField(
            'image', 'image', [
            'title' => __('Collection Image'),
            'label' => __('Collection Image'),
            'name' => 'image',
            'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );


        $elements['short_description'] = $fieldset->addField(
            'short_description', 'textarea', [
            'name' => 'short_description',
            'label' => __('Short description'),
            'title' => __('Short description'),
            'required' => true,
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
    public function getCollection()
    {
        return $this->_coreRegistry->registry('collection');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getCollection()->getId() ? __("Edit Collection '%1'", $this->escapeHtml($this->getCollection()->getName())) : __('New Collection');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Collection Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Collection Information');
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

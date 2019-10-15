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

namespace Ss\Designer\Block\Adminhtml\Designer\Edit\Tab;

class Designer extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
    protected $_designer;
    protected $_typeFactory;
    protected $_wysiwygConfig;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\DataObjectFactory $objectFactory
     * @param \Ss\Designer\Model\Designer $designer
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Ss\Designer\Model\Designer $designer,
        \Ss\Designer\Model\TypeFactory $typeFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_objectFactory = $objectFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_designer = $designer;
        $this->_typeFactory = $typeFactory;
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
        $model = $this->getDesigner();

        if ($designerId = $this->getRequest()->getParam('current_designer_id')) {
            $model->setSliderId($designerId);
        }

        $dataObj->addData($model->getData());


        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('designer');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Designer Information')]);

        if ($model->getId()) {
            $fieldset->addField('designer_id', 'hidden', ['name' => 'designer_id']);
            $fieldset->addField('option_id', 'hidden', ['name' => 'option_id']);
        }

        $elements = [];
        $elements['name'] = $fieldset->addField('name', 'text', ['name' => 'name', 'label' => __('Title'), 'title' => __('Title'), 'required' => true,]);
        $elements['first_name'] = $fieldset->addField('first_name', 'text', ['name' => 'first_name', 'label' => __('First Name'), 'title' => __('First Name'), 'required' => false,]);
        $elements['last_name'] = $fieldset->addField('last_name', 'text', ['name' => 'last_name', 'label' => __('Last Name'), 'title' => __('Last Name'), 'required' => false,]);
        $elements['url_key'] = $fieldset->addField('url_key', 'text', ['name' => 'url_key', 'label' => __('Url key'), 'title' => __('Url key'), 'required' => true]);
        $elements['is_backup'] = $fieldset->addField('is_backup', 'select', ['name' => 'is_backup', 'label' => __('Create Permanent Redirect for old URL'), 'title' => __('Create Permanent Redirect for old URL'), 'required' => false, 'values' => \Ss\Designer\Model\Designer::BOOLEAN_OPTION, 'value' => 1]);
        $elements['gender'] = $fieldset->addField('gender', 'select', ['name' => 'gender', 'label' => __('Gender'), 'title' => __('Gender'), 'required' => false, 'values' => \Ss\Designer\Model\Designer::GENDER_OPTION]);

        $elements['is_active'] = $fieldset->addField('is_active', 'select', ['label' => __('Status'),'title' => __('Designer Status'),'name' => 'is_active','required' => false,'options' => \Ss\Designer\Model\Designer::ACTIVE_OPTION]);

        $listTypes = $this->_typeFactory->create()->getListOptions();
        $elements['type_id'] = $fieldset->addField('type_id', 'select', ['name' => 'type_id', 'label' => __('Type'), 'title' => __('Type'), 'required' => true, 'values' => $listTypes]);
        $elements['image'] = $fieldset->addField('image', 'image', ['title' => __('Designer Image'), 'label' => __('Designer Image'), 'name' => 'image', 'note' => 'Allow image type: jpg, jpeg, gif, png',]);
        $elements['image_product'] = $fieldset->addField('image_product', 'image', ['title' => __('Product Image'), 'label' => __('Product Image'), 'name' => 'image_product', 'note' => 'Allow image type: jpg, jpeg, gif, png',]);
        $elements['email'] = $fieldset->addField('email', 'text', ['name' => 'email', 'label' => __('Email'), 'title' => __('Email'), 'required' => false]);
        $elements['address'] = $fieldset->addField('address', 'textarea', ['name' => 'address', 'label' => __('Address'), 'title' => __('Address'), 'required' => false,]);
        $elements['why_love'] = $fieldset->addField('why_love', 'textarea', ['name' => 'why_love', 'label' => __('Why love'), 'title' => __('why love'), 'required' => false,]);
        $elements['quote'] = $fieldset->addField('quote', 'textarea', ['name' => 'quote', 'label' => __('Quote'), 'title' => __('Quote'), 'required' => false,]);
        $elements['description'] = $fieldset->addField('description', 'editor', ['name' => 'description', 'label' => __('Description'), 'title' => __('Description'), 'style' => 'height:15em', 'required' => false, 'config' => $wysiwygConfig]);
        $elements['order_note'] = $fieldset->addField('order_note', 'textarea', ['name' => 'order_note', 'label' => __('Order Note'), 'title' => __('Order Note'), 'required' => false,]);
        $elements['payment_note'] = $fieldset->addField('payment_note', 'textarea', ['name' => 'payment_note', 'label' => __('Payment Note'), 'title' => __('Payment Note'), 'required' => false,]);
        $elements['meta_title'] = $fieldset->addField('meta_title', 'text', ['name' => 'meta_title', 'label' => __('Meta Title'), 'title' => __('Meta Title'), 'required' => false,]);
        $elements['meta_keywords'] = $fieldset->addField('meta_keywords', 'textarea', ['name' => 'meta_keywords', 'label' => __('Meta Keywords'), 'title' => __('Meta Keywords'), 'required' => false,]);
        $elements['meta_description'] = $fieldset->addField('meta_description', 'textarea', ['name' => 'meta_description', 'label' => __('Meta Description'), 'title' => __('Meta Description'), 'required' => false,]);

        $form->addValues($dataObj->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getDesigner()
    {
        return $this->_coreRegistry->registry('designer');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getDesigner()->getId() ? __("Edit Designer '%1'", $this->escapeHtml($this->getDesigner()->getName())) : __('New Designer');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Designer Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Designer Information');
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

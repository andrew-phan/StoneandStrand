<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Banners chooser for Banner Rotator widget
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magefan\Blog\Block\Adminhtml\Post\Widget;

use Magento\Backend\Block\Widget\Grid\Column;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;
    protected $_postCollection;
    protected $_categoryFactory;

    /**
     * 
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magefan\Blog\Model\ResourceModel\Post\Collection $postCollectionFactory
     * @param \Magefan\Blog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, 
        \Magento\Backend\Helper\Data $backendHelper, 
        \Magefan\Blog\Model\ResourceModel\Post\Collection $postCollectionFactory, 
        \Magefan\Blog\Model\CategoryFactory $categoryFactory, 
        \Magento\Framework\Data\Form\Element\Factory $elementFactory, array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->_elementFactory = $elementFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_postCollection = $postCollectionFactory;
    }

    /**
     * Store selected banner Ids
     * Used in initial setting selected banners
     *
     * @var array
     */
    protected $_selectedPosts = [];

    /**
     * Store hidden banner ids field id
     *
     * @var string
     */
    protected $_elementValueId = '';

    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    public function _construct() {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('post_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

        $this->setDefaultFilter(['in_posts' => 1]);
    }

    protected function _prepareCollection() {
        $this->_postCollection->setRenderCategory(TRUE);
        $this->setCollection($this->_postCollection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $this->_elementValueId = "{$element->getId()}";

        $this->_selectedPosts = explode(',', $element->getValue());

        //Create hidden field that store selected banner ids
        $hidden = $this->_elementFactory->create('hidden', ['data' => $element->getData()]);
        $hidden->setId($this->_elementValueId)->setForm($element->getForm())->addClass("required-entry");
        $hiddenHtml = $hidden->getElementHtml();


        $element->setValue('')->setValueClass('value2');
        $element->setData('css_class', 'grid-chooser');
        $element->setData('after_element_html', $hiddenHtml . $this->toHtml());
        $element->setData('no_wrap_as_addon', true);

        return $element;
    }

    /**
     * Create grid columns
     *
     * @return $this
     */
    protected function _prepareColumns() {

        $this->addColumn(
            'in_posts', [
            'header_css_class' => 'col-select',
            'column_css_class' => 'col-select',
            'type' => 'checkbox',
            'name' => 'in_posts',
            'values' => $this->getSelectedPosts(),
            'index' => 'post_id'
            ]
        );

        $this->addColumn(
            'post_id', [
            'header' => __('Post ID'),
//          'type' => 'number', 
            'index' => 'post_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'width' => '70px',
            ]
        );

        $this->addColumn(
            'title', [
            'header' => __('Title'),
            'index' => 'title',
            'class' => 'xxx',
            'width' => '50px',
            ]
        );

        $treeCategory = $this->_categoryFactory->create()->getListOptionCategory(FALSE);
        $this->addColumn(
            'category', [
                'header' => __('Category'),
                'index' => 'category',
                'type' => 'options',
                'options'   =>  $treeCategory,
                'class' => 'xxx',
                'width' => '50px',
                'renderer' => 'Magefan\Blog\Block\Adminhtml\Post\Helper\Renderer\Category',
            ]
        );

        $this->addColumn(
            'position', [
            'header' => __('Position'),
            'name' => 'position',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'position',
            'editable' => true,
            'filter' => false,
            'edit_only' => true,
            'sortable' => false,
            'width' => '50px',
            ]
        );

//        $this->addColumnsOrder('position', 'order_banner');

        return parent::_prepareColumns();
    }

    /**
     * Set custom filter for in banner flag
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_posts') {
            $bannerIds = $this->getSelectedPosts();
//            var_dump($bannerIds);die;
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
//            if ($column->getFilter()->getValue()) {
//                $this->getCollection()->addBannerIdsFilter($bannerIds);
//            }
//            else {
//                if ($bannerIds) {
//                    $this->getCollection()->addBannerIdsFilter($bannerIds, true);
//                }
//            }
        }else{
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Disable mass action functionality
     *
     * @return $this
     */
    protected function _prepareMassaction() {
        return $this;
    }

    /**
     * Adds additional parameter to URL for loading only banners grid
     *
     * @return string
     */
    public function getGridUrl() {
//      return $this->getUrl('*/*/grid', ['_current' => true]);
        return $this->getUrl(
                'blog/post_widget/chooser', [
                '_current' => true,
//                'selected_posts' => join(',', $this->getSelectedPosts())
                ]
        );
    }

    /**
     * Setter
     *
     * @param array $selectedPosts
     * @return $this
     */
    public function setSelectedPosts($selectedPosts) {
        if (is_string($selectedPosts)) {
            $selectedPosts = explode(',', $selectedPosts);
        }
        if (is_array($selectedPosts) && count($selectedPosts) == 1 && empty($selectedPosts[0])) {
            $selectedPosts = 0;
        }
        $this->_selectedPosts = $selectedPosts;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedPosts() {

        if ($selectedPosts = $this->getRequest()->getParam('selected_posts', $this->_selectedPosts)) {

            $this->setSelectedPosts($selectedPosts);
        }
        return $this->_selectedPosts;
    }

    /**
     * Grid row init js callback
     *
     * @return string
     */
    public function getRowInitCallback() {
        return '
        function(grid, row){
            if(!grid.selPostsIds){
                grid.selPostsIds = {};
                if($(\'' .
            $this->_elementValueId .
            '\').value != \'\'){
                    var elementValues = $(\'' .
            $this->_elementValueId .
            '\').value.split(\',\');
                    for(var i = 0; i < elementValues.length; i++){
                        grid.selPostsIds[elementValues[i]] = i+1;
                    }
                }
                grid.reloadParams = {};
                grid.reloadParams[\'selected_posts[]\'] = Object.keys(grid.selPostsIds);
            }
            var inputs      = Element.select($(row), \'input\');
            var checkbox    = inputs[0];
            var position    = inputs[1];
            var bannersNum  = grid.selPostsIds.length;
            var bannerId    = checkbox.value;

            inputs[1].checkboxElement = checkbox;

            var indexOf = Object.keys(grid.selPostsIds).indexOf(bannerId);
            if(indexOf >= 0){
                checkbox.checked = true;
                if (!position.value) {
                    position.value = indexOf + 1;
                }
            }

            Event.observe(position,\'change\', function(){
                var checkb = Element.select($(row), \'input\')[0];
                if(checkb.checked){
                    grid.selPostsIds[checkb.value] = this.value;
                    var idsclone = Object.clone(grid.selPostsIds);
                    var bans = Object.keys(grid.selPostsIds);
                    var pos = Object.values(grid.selPostsIds).sort(sortNumeric);
                    var banners = [];
                    var k = 0;

                    for(var j = 0; j < pos.length; j++){
                        for(var i = 0; i < bans.length; i++){
                            if(idsclone[bans[i]] == pos[j]){
                                banners[k] = bans[i];
                                k++;
                                delete(idsclone[bans[i]]);
                                break;
                            }
                        }
                    }
                    $(\'' .
            $this->_elementValueId .
            '\').value = banners.join(\',\');
                }
            });
        }
        ';
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback() {
        return '
            function (grid, event) {            
                if(!grid.selPostsIds){
                    grid.selPostsIds = {};
                }

                var trElement   = Event.findElement(event, "tr");
                var isInput     = Event.element(event).tagName == \'INPUT\';
                var inputs      = Element.select(trElement, \'input\');
                var checkbox    = inputs[0];
                var position    = inputs[1].value || 1;
                var checked     = isInput ? checkbox.checked : !checkbox.checked;
                checkbox.checked = checked;
                var bannerId    = checkbox.value;

                if(checked){
                    if(Object.keys(grid.selPostsIds).indexOf(bannerId) < 0){
                        grid.selPostsIds[bannerId] = position;
                    }
                }
                else{
                    delete(grid.selPostsIds[bannerId]);
                }

                var idsclone = Object.clone(grid.selPostsIds);
                var bans = Object.keys(grid.selPostsIds);
                var pos = Object.values(grid.selPostsIds).sort(sortNumeric);
                var banners = [];
                var k = 0;
                for(var j = 0; j < pos.length; j++){
                    for(var i = 0; i < bans.length; i++){
                        if(idsclone[bans[i]] == pos[j]){
                            banners[k] = bans[i];
                            k++;
                            delete(idsclone[bans[i]]);
                            break;
                        }
                    }
                }
                

                $(\'' .
            $this->_elementValueId .
            '\').value = banners.join(\',\');
                grid.reloadParams = {};
                grid.reloadParams[\'selected_posts[]\'] = banners;
            }
        ';
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback() {
        return 'function (grid, element, checked) {
          
                    if(!grid.selPostsIds){
                        grid.selPostsIds = {};
                    }
                    var checkbox    = element;

                    checkbox.checked = checked;
                    var bannerId    = checkbox.value;
                    if(bannerId == \'on\'){
                        return;
                    }
                    var trElement   = element.up(\'tr\');
                    var inputs      = Element.select(trElement, \'input\');
                    var position    = inputs[1].value || 1;

                    if(checked){
                        if(Object.keys(grid.selPostsIds).indexOf(bannerId) < 0){
                            grid.selPostsIds[bannerId] = position;
                        }
                    }
                    else{
                        delete(grid.selPostsIds[bannerId]);
                    }

                    var idsclone = Object.clone(grid.selPostsIds);
                    var bans = Object.keys(grid.selPostsIds);
                    var pos = Object.values(grid.selPostsIds).sort(sortNumeric);
                    var banners = [];
                    var k = 0;
                    for(var j = 0; j < pos.length; j++){
                        for(var i = 0; i < bans.length; i++){
                            if(idsclone[bans[i]] == pos[j]){
                                banners[k] = bans[i];
                                k++;
                                delete(idsclone[bans[i]]);
                                break;
                            }
                        }
                    }
                    $(\'' .
            $this->_elementValueId .
            '\').value = banners.join(\',\');
                    grid.reloadParams = {};
                    grid.reloadParams[\'selected_posts[]\'] = banners;
                }';
    }

}


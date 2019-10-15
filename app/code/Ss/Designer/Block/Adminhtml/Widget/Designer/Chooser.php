<?php

namespace Ss\Designer\Block\Adminhtml\Widget\Designer;

use Magento\Backend\Block\Widget\Grid\Column;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Chooser extends \Ss\Designer\Block\Adminhtml\Designer\Grid
{

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Banner\Model\ResourceModel\Banner\CollectionFactory $bannerColFactory
     * @param \Magento\Banner\Model\Config $bannerConfig
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ss\Designer\Model\ResourceModel\Designer\Collection $designerCollection,
        \Ss\Designer\Model\ResourceModel\Tags\Collection $tagCollection,
        \Ss\Designer\Model\ResourceModel\Type\Collection $typeCollection,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $designerCollection, $tagCollection, $typeCollection, $data);

        $this->_elementFactory = $elementFactory;
    }

    /**
     * Store selected banner Ids
     * Used in initial setting selected banners
     *
     * @var array
     */
    protected $_selectedDesigners = [];

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
    public function _construct()
    {
        parent::_construct();

        $this->setDefaultFilter(['in_designers' => 1]);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_elementValueId = "{$element->getId()}";

        $this->_selectedDesigners = explode(',', $element->getValue());

        //Create hidden field that store selected banner ids
        $hidden = $this->_elementFactory->create('hidden', ['data' => $element->getData()]);
        $hidden->setId($this->_elementValueId)->setForm($element->getForm());
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
    protected function _prepareColumns()
    {

        $this->addColumn(
            'in_designers', [
            'header_css_class' => 'col-select',
            'column_css_class' => 'col-select',
            'type' => 'checkbox',
            'name' => 'in_designers',
            'values' => $this->getSelectedDesigners(),
            'index' => 'designer_id'
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
            'sortable' => false
            ]
        );

        // Set position for this column.
        $this->addColumnsOrder('position', 'edit');

        return parent::_prepareColumns();
    }

    /**
     * Set custom filter for in banner flag
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_designers') {
            $designerIds = $this->getSelectedDesigners();

            if (!empty($designerIds)) {
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('designer_id', [ 'IN' => $designerIds]);
                } else {
                    if ($designerIds) {
                        $this->getCollection()->addFieldToFilter('designer_id', [ 'IN' => $designerIds]);
                    }
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Disable mass action functionality
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Adds additional parameter to URL for loading only banners grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
                'stone_designer/designer_widget/chooser', [
                '_current' => true,
                ]
        );
    }

    /**
     * Setter
     *
     * @param array $selectedDesigners
     * @return $this
     */
    public function setSelectedDesigners($selectedDesigners)
    {
        if (is_string($selectedDesigners)) {
            $selectedDesigners = explode(',', $selectedDesigners);
        }
        if (is_array($selectedDesigners) && count($selectedDesigners) == 1 && empty($selectedDesigners[0])) {
            $selectedDesigners = 0;
        }
        $this->_selectedDesigners = $selectedDesigners;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedDesigners()
    {

        if ($selectedDesigners = $this->getRequest()->getParam('selected_designers', $this->_selectedDesigners)) {

            $this->setSelectedDesigners($selectedDesigners);
        }
        return $this->_selectedDesigners;
    }

    /**
     * Grid row init js callback
     *
     * @return string
     */
    public function getRowInitCallback()
    {
        return '
        function(grid, row){
            if(!grid.selBannersIds){
                grid.selBannersIds = {};
                if($(\'' .
            $this->_elementValueId .
            '\').value != \'\'){
                    var elementValues = $(\'' .
            $this->_elementValueId .
            '\').value.split(\',\');
                    for(var i = 0; i < elementValues.length; i++){
                        grid.selBannersIds[elementValues[i]] = i+1;
                    }
                }
                grid.reloadParams = {};
                grid.reloadParams[\'selected_designers[]\'] = Object.keys(grid.selBannersIds);
            }
            var inputs      = Element.select($(row), \'input\');
            var checkbox    = inputs[0];
            var position    = inputs[1];
            var bannersNum  = grid.selBannersIds.length;
            var bannerId    = checkbox.value;

            inputs[1].checkboxElement = checkbox;

            var indexOf = Object.keys(grid.selBannersIds).indexOf(bannerId);
            if(indexOf >= 0){
                checkbox.checked = true;
                if (!position.value) {
                    position.value = indexOf + 1;
                }
            }

            Event.observe(position,\'change\', function(){
                var checkb = Element.select($(row), \'input\')[0];
                if(checkb.checked){
                    grid.selBannersIds[checkb.value] = this.value;
                    var idsclone = Object.clone(grid.selBannersIds);
                    var bans = Object.keys(grid.selBannersIds);
                    var pos = Object.values(grid.selBannersIds).sort(sortNumeric);
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
    public function getRowClickCallback()
    {
        return '
            function (grid, event) {
                if(!grid.selBannersIds){
                    grid.selBannersIds = {};
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
                    if(Object.keys(grid.selBannersIds).indexOf(bannerId) < 0){
                        grid.selBannersIds[bannerId] = position;
                    }
                }
                else{
                    delete(grid.selBannersIds[bannerId]);
                }

                var idsclone = Object.clone(grid.selBannersIds);
                var bans = Object.keys(grid.selBannersIds);
                var pos = Object.values(grid.selBannersIds).sort(sortNumeric);
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
                grid.reloadParams[\'selected_designers[]\'] = banners;
            }
        ';
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        return 'function (grid, element, checked) {

                    if(!grid.selBannersIds){
                        grid.selBannersIds = {};
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
                        if(Object.keys(grid.selBannersIds).indexOf(bannerId) < 0){
                            grid.selBannersIds[bannerId] = position;
                        }
                    }
                    else{
                        delete(grid.selBannersIds[bannerId]);
                    }

                    var idsclone = Object.clone(grid.selBannersIds);
                    var bans = Object.keys(grid.selBannersIds);
                    var pos = Object.values(grid.selBannersIds).sort(sortNumeric);
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
                    grid.reloadParams[\'selected_designers[]\'] = banners;
                }';
    }

}

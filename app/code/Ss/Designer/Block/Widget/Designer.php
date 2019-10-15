<?php

namespace Ss\Designer\Block\Widget;

/**
 * Class widget designer.
 */
class Designer extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    const DESIGNER_TYPE = 'designer';
    const TAG_TYPE = 'tag';

    protected $_designerCollectionFactory;
    protected $_tagCollectionFactory;
    protected $_designerHelper;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Designer\Model\ResourceModel\Designer\CollectionFactory $designerCollectionFactory
     * @param \Ss\Designer\Model\ResourceModel\Tags\CollectionFactory $tagCollectionFactory
     * @param \Ss\Designer\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
        \Ss\Designer\Model\ResourceModel\Designer\CollectionFactory $designerCollectionFactory,
        \Ss\Designer\Model\ResourceModel\Tags\CollectionFactory $tagCollectionFactory,
        \Ss\Designer\Helper\Data $helper,
        array $data = array()
    )
    {
        $this->_designerCollectionFactory = $designerCollectionFactory;
        $this->_tagCollectionFactory = $tagCollectionFactory;
        $this->_designerHelper = $helper;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/designer_menu.phtml');
        $this->addData([
            'cache_lifetime' => 86400,
        ]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {

        $template = $this->getTemplate();
        $template = explode('/', $template);
        $name = array_pop($template);
        $title = str_replace(' ', '_', $this->getData('title'));
        
        return [
            'BLOCK_TPL_' . $name . '_' . $title,
            $this->_storeManager->getStore()->getCode(),
            $this->getTemplateFile(),
            'template' => $this->getTemplate()
        ];
    }

    /**
     * @todo to get designer helper
     * @return type
     */
    public function getDesignerHelper()
    {
        return $this->_designerHelper;
    }

    /**
     * @todo to get title from widget
     * @return type
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * @todo to get display type from widget
     * @return type
     */
    public function getDisplayType()
    {
        return $this->getData('display_type');
    }

    /**
     * @todo to get width from widget
     * @return type
     */
    public function getWidth()
    {
        return $this->getData('width');
    }

    /**
     * @todo to get list designer id from widget
     * @return type
     */
    public function getDesignerIds()
    {
        $listIds = explode(',', $this->getData('designer_ids'));

        return $listIds;
    }

    /**
     * @todo to get list tag id from widget
     * @return type
     */
    public function getTagIds()
    {
        $listIds = explode(',', $this->getData('tag_ids'));

        return $listIds;
    }

    /**
     * @todo To get collection follow display type
     * @return type
     */
    public function getCollections()
    {
        $limit = \Ss\Designer\Model\Designer::LIMIT_ITEM_MENU;
        $displayType = $this->getDisplayType();
        $collections = [];
        switch ($displayType) {
            case static::DESIGNER_TYPE:
                $listIds = $this->getDesignerIds();
                $collections = $this->_designerCollectionFactory->create()
                    ->addFieldToFilter('is_active', ["IN" => 1])
                    ->addFieldToFilter('designer_id', ['IN' => $listIds]);
                break;

            case static::TAG_TYPE:
                $listIds = $this->getTagIds();
                $collections = $this->_tagCollectionFactory->create()
                    ->addFieldToFilter('tag_id', ['IN' => $listIds]);
                break;
            default:
                break;
        }

        $collections->setPageSize($limit)->setOrder('name', 'ASC');
        return $collections;
    }

    /**
     * @todo to get media url
     * @return type
     */
    public function getMediaUrl()
    {
        return $this->_designerHelper->getSrcMediaImage();
    }

}

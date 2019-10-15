<?php

namespace Ss\Designer\Block\Widget;

/**
 * Block Widget designer listing
 */
class DesignerListing extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    protected $_designerCollectionFactory;
    protected $_tagsCollectionFactory;
    protected $_typeFactory;
    protected $_listDesigner;
    protected $_listTag;
    protected $_designerHelper;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Designer\Helper\Data $designerHelper
     * @param \Ss\Designer\Model\ResourceModel\Designer\CollectionFactory $designerCollectionFactory
     * @param \Ss\Designer\Model\ResourceModel\Tags\CollectionFactory $tagsCollectionFactory
     * @param \Ss\Designer\Model\TypeFactory $typeFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
        \Ss\Designer\Helper\Data $designerHelper,
        \Ss\Designer\Model\ResourceModel\Designer\CollectionFactory $designerCollectionFactory,
        \Ss\Designer\Model\ResourceModel\Tags\CollectionFactory $tagsCollectionFactory,
        \Ss\Designer\Model\TypeFactory $typeFactory,
        array $data = array()
    )
    {
        $this->_designerCollectionFactory = $designerCollectionFactory;
        $this->_tagsCollectionFactory = $tagsCollectionFactory;
        $this->_typeFactory = $typeFactory;
        $this->_designerHelper = $designerHelper;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate($this->getTemplateName());
        $this->addData([
            'cache_lifetime' => 86400,
        ]);
    }

    /**
     * @todo to get template name
     * @return type
     */
    public function getTemplateName()
    {
        return !empty($this->getData('template')) ? $this->getData('template') : 'widget/designer/listing/modern.phtml';
    }

    /**
     * @todo to get Title widget
     * @return type
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * @todo To get link
     * @return type
     */
    public function getLink()
    {
        return $this->getData('link');
    }

    /**
     * @todo to get number item column
     * @return type
     */
    public function getNumberItemColumn()
    {
        return $this->getData('number_item_column');
    }

    /**
     * @todo get type by id
     * @return type
     */
    public function getTypeId()
    {
        return $this->getData('type_id');
    }

    /**
     * @todo get post by ids
     * @return type
     */
    public function getPostIds()
    {
        $postIds = $this->getData('post_ids');
        if (!empty($postIds)) {
            return explode(',', $postIds);
        }
        return '';
    }

    /**
     * @todo to get post by id
     * @param type $postId
     * @return boolean
     */
    public function getPostById($postId)
    {
        if (!isset($postId) || empty($postId)) {
            return FALSE;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('Magefan\Blog\Model\Post')->load($postId);
    }

    /**
     * @todo to get type
     * @return boolean
     */
    public function getType()
    {
        $typeId = $this->getTypeId();
        if (!$typeId) {
            return FALSE;
        }

        return $this->_typeFactory->create()->load($typeId);
    }

    /**
     * @todo to get designer ids
     * @return type
     */
    public function getDesignerIds()
    {
        $listIds = explode(',', $this->getData('designer_ids'));

        return $listIds;
    }

    /**
     * @todo to get designer collections
     * @return type
     */
    public function getDesignerCollections()
    {
        if (!is_null($this->_listDesigner)) {
            return $this->_listDesigner;
        }

        $templateName = $this->getTemplateName();
        switch ($templateName) {
            case "widget/designer/listing/featured_designer.phtml":
                $designerIds = $this->getDesignerIds();
                if (!empty($designerIds)) {
                    $this->_listDesigner = $this->_designerCollectionFactory->create()
                        ->addFieldToFilter('designer_id', ["IN" => $designerIds])
                        ->addFieldToFilter('is_active', ["IN" => 1])
                        ->setOrder('name', 'ASC');
                }
                break;
            default:
                $typeId = $this->getTypeId();
                if ($typeId) {
                    $this->_listDesigner = $this->_designerCollectionFactory->create()
                        ->addFieldToFilter('type_id', ["IN" => $typeId])
                        ->addFieldToFilter('is_active', ["IN" => 1])
                        ->setOrder('name', 'ASC');
                }
                break;
        }

        if (!$this->_listDesigner || empty($this->_listDesigner) || is_null($this->_listDesigner)) {
            return [];
        }

        return $this->_listDesigner;
    }

    /**
     * @todo get tag collections
     * @return type
     */
    public function getTagCollections()
    {
        if (!is_null($this->_listTag)) {
            return $this->_listTag;
        }

        $flag = false;
        $typeId = $this->getTypeId();
        if ($typeId) {
            $designerCollection = $this->getDesignerCollections();
            if (!$designerCollection || empty($designerCollection)) {
                $flag = true;
            }
        } else {
            $flag = true;
        }


        if ($flag) {
            return [];
        }

        $designerIds = $designerCollection->getAllIds();
        return $this->_listTag = $this->_tagsCollectionFactory->create()->addFieldToFilter('designer_id', ["IN" => $designerIds])->setOrder('name', 'ASC');
    }

    /**
     * @todo get list designer id by tag id
     * @param type $tagId
     * @return string
     */
    public function getListDesignerIdByTagId($tagId)
    {
        $collection = $this->_designerCollectionFactory->create()
            ->addFieldToSelect("designer_id")
            ->addFieldToFilter('is_active', ["IN" => 1])
            ->addAttributeToFilter("tag_id", ['IN' => $tagId]);
        if ($collection->getSize() > 0) {
            return implode(',', $collection->getAllIds());
        }
        return '';
    }

    /**
     * @todo to get src image
     * @return type
     */
    public function getSrcImageTransparent()
    {
        return $this->_designerHelper->getSrcImageTransparent();
    }

    /**
     * @todo to get media url
     * @return type
     */
    public function getMediaUrl()
    {
        return $this->_designerHelper->getSrcMediaImage();
    }

    /**
     * @todo to get jewelry page
     * @return type
     */
    public function getJewelryUrlPage()
    {
        return $this->_designerHelper->getJewelryUrlPage();
    }

    /**
     * @todo to get caosuke url page
     * @return type
     */
    public function getCapsuleUrlPage()
    {
        return $this->_designerHelper->getCapsuleUrlPage();
    }


    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {

        $template = $this->getTemplateName();
        $template = explode('/', $template);
        $name = array_pop($template);
        return [
            'BLOCK_TPL_' . $name,
            $this->_storeManager->getStore()->getCode(),
            $this->getTemplateFile(),
            'template' => $this->getTemplate()
        ];

    }

}

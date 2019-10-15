<?php

namespace Ss\Designer\Block;

/**
 * Block detail designer info.
 */
class DesignerView extends \Magento\Catalog\Block\Product\AbstractProduct implements
\Magento\Framework\DataObject\IdentityInterface
{

    /**
     * Tag collection factory.
     * @var type 
     */
    protected $_tagsCollectionFactory;
    protected $_typeFactory;

    /**
     * Construct
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ss\Designer\Model\Designer $designer
     * @param \Ss\Designer\Model\DesignerFactory $designerFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context,
        \Ss\Designer\Model\ResourceModel\Tags\CollectionFactory $tagsCollectionFactory,
        \Ss\Designer\Model\Designer $designer,
        \Ss\Designer\Model\DesignerFactory $designerFactory,
        \Ss\Designer\Model\TypeFactory $typeFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_designer = $designer;
        $this->_designerFactory = $designerFactory;
        $this->_tagsCollectionFactory = $tagsCollectionFactory;
        $this->_typeFactory = $typeFactory;
    }

    /**
     * @return \Ashsmith\Blog\Model\Post
     */
    public function getDesigner()
    {
        if (!$this->hasData('designer')) {
            if ($this->getDesignerId()) {
                $post = $this->_designerFactory->create();
            } else {
                $post = $this->_designer;
            }
            $this->setData('designer', $post);
        }

        return $this->getData('designer');
    }

    /**
     * @todo To get list tag from designer id
     * @return type
     */
    public function getTagCollections()
    {
        $designer = $this->getDesigner();
        return $this->_tagsCollectionFactory->create()
                ->addFieldToFilter('designer_id', ["IN" => $designer->getDesignerId()])
                ->setOrder('name', 'ASC');
    }

    /**
     * @todo To get Type of designer
     */
    public function getType()
    {
        $designer = $this->getDesigner();
        return $this->_typeFactory->create()->load($designer->getTypeId());
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Ss\Designer\Model\Designer::CACHE_TAG . '_' . $this->getDesigner()->getId()];
    }

}

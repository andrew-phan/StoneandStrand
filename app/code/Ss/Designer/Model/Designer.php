<?php

namespace Ss\Designer\Model;

use Magento\Framework\DataObject\IdentityInterface;

class Designer extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'ss_designer';
    const ATTRIBUTE_CODE = 'ss_designer';   // Attribute product
    const BASE_MEDIA_PATH = 'designer';     // folder contain image upload
    const LIMIT_ITEM_MENU = 9;              // Limit item will show on menu
    const PARAM_FILTER_CATEGORY = 'cat';    // Default param of magento
    const GENDER_OPTION = [1 => 'Male', 2 => 'Female', 3 => 'Team'];
    const BOOLEAN_OPTION = [0 => 'NO', 1 => 'YES'];
    const ACTIVE_OPTION = [0 => 'Disabled',1 => 'Enabled'];
    const PREFIX_URL_DESIGNER = 'designers';    // Prefix url of detail designer page
    const PREFIX_TITLE_DESIGNER = 'Shop ';    // Prifix meta title of detail designer page
    const SUFFIX_TITLE_DESIGNER = ' Designer Fine Jewelry | Stone & Strand';    // Suffix meta title of detail designer page
    const SUFFIX_URL_DESIGNER = '-jewelry';     // Suffix url of detail designer page
    const PREFIX_URL_DESIGNER_VINTAGE = 'vintage-';     // Suffix url of detail designer page
    const GET_URL_DESIGNER = 'url_designer';    // Flag to get url product follow designer
    const PARAM_CHANGE_URL_KEY = 'is_change_url_key';   // Param is change url key
    const PARAM_CHANGE_TYPE = 'is_change_type'; // Param is change type
    const PARAM_BACKUP_URL = 'is_back_up_url';      // Param is backup url rewrite
    const PREFIX_TARGET_PATH = 'designers/view/index/designer_id/';  // Prefix target path
    const KEY_NO_DESIGNER = 'non_designer';      // Key for no designer

    protected $_listTagIds = NULL;
    protected $_listTagNames = NULL;
    protected $_tagsFactory = NULL;
    protected $_prouctCollectionFactory = NULL;
    protected $_urlBuilder;
    protected $_themeHelper;
    protected $_typeFactory;
    protected $_typeObject;
    protected $_urlPath;
    protected $_request;

    public function __construct(
    \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Helper\Context $contextHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $prouctCollectionFactory,
        \Ss\Designer\Model\TagsFactory $tagFactory,
        \Ss\Theme\Helper\Data $helper,
        \Ss\Designer\Model\TypeFactory $typeFactory,
        \Magento\Framework\App\Action\Context $contextRequest,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,        
        array $data = []
    )
    {
        $this->_tagsFactory = $tagFactory;
        $this->_urlBuilder = $contextHelper->getUrlBuilder();
        $this->_prouctCollectionFactory = $prouctCollectionFactory;
        $this->_themeHelper = $helper;
        $this->_typeFactory = $typeFactory;
        $this->_request = $contextRequest->getRequest();

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init('Ss\Designer\Model\ResourceModel\Designer');
    }

    /**
     * @todo To get list Tag id
     * @return type
     */
    public function getTagIds($designer_id = 0)
    {
        if (is_null($this->_listTagIds)) {
            if (empty($designer_id)) {
                $designer_id = $this->getId();
            }
            $designerResource = $this->getResource();
            $this->_listTagIds = $designerResource->getTagIds($designer_id);
        }

        return $this->_listTagIds;
    }

    /**
     * @todo To get list Tags Name.
     * @param type $designer_id
     * @return type
     */
    public function getTagNames($designer_id = 0)
    {
        if (is_null($this->_listTagNames)) {
            if (empty($designer_id)) {
                $designer_id = $this->getId();
            }

            if (is_null($this->_listTagIds)) {
                $this->_listTagIds = $this->getTagIds($designer_id);
            }

            $this->_listTagNames = $this->_tagsFactory->create()->getCollection()
                ->addFieldToFilter('tag_id', ['in' => $this->_listTagIds]);

            $listTag = [];
            foreach ($this->_listTagNames as $item) {
                $listTag[$item->getTagId()] = $item->getName();
            }

            $this->_listTagNames = $listTag;
        }

        return $this->_listTagNames;
    }

    /**
     * To get detail designer page
     * @return type
     */
    public function getUrl()
    {

        return $this->_urlBuilder->getBaseUrl('/') . $this->getUrlPath();
    }

    /**
     * @todo to get Url path designer
     * @return type
     */
    public function getUrlPath($forceNew = FALSE)
    {
        if (!$this->_urlPath || $forceNew) {
            $prefixVintage = ($this->isTypeVintage($forceNew)) ? self::PREFIX_URL_DESIGNER_VINTAGE : '';
            $this->_urlPath = self::PREFIX_URL_DESIGNER . '/' . $prefixVintage . $this->getUrlKey() . self::SUFFIX_URL_DESIGNER;
        }

        return $this->_urlPath;
    }

    /**
     * @todo to get target path
     * @return type
     */
    public function getTargetPath()
    {
        return static::PREFIX_TARGET_PATH . $this->getDesignerId();
    }

    /**
     * Check if post url key exists
     * return post id if post exists
     *
     * @param string $url_key
     * @return int
     */
    public function checkExistUrlKey($url_key)
    {
        return $this->_getResource()->checkExistUrlKey($url_key);
    }

    /**
     * @todo To get feature image
     * @return type
     */
    public function getFeatureImageUrl()
    {
        $featureImage = $this->getImage();
        if (empty($featureImage)) {
                    
            return $this->_themeHelper->getPlaceHolderImage();
        }

        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA, '_secure' => $this->_request->isSecure()]) . $featureImage;
    }

    /**
     * @todo To get image product
     * @return type
     */
    public function getImageProductUrl()
    {
        $featureImage = $this->getImageProduct();
        if (empty($featureImage)) {
            return $this->_themeHelper->getPlaceHolderImage();
        }

        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA, '_secure' => $this->_request->isSecure()]) . $featureImage;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [static::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @todo To get Type of designer
     * @return type
     */
    public function getTypeDesigner($forceNew = FALSE)
    {
        if (!$this->_typeObject || $forceNew) {            
            $this->_typeObject = $this->_typeFactory->create()->load($this->getTypeId());
        }
        return $this->_typeObject;
    }

    /**
     * @todo to check this designer is vintage
     * @return type
     */
    public function isTypeVintage($forceNew = FALSE)
    {
        return $this->getTypeDesigner($forceNew)->getIsVintage();
    }

    /**
     * Get designer By Name
     *
     * @param string $designerName
     * @return int
     */
    public function getDesignerByName($designerName)
    {
        return $this->_getResource()->getDesignerByName($designerName);
    }

}

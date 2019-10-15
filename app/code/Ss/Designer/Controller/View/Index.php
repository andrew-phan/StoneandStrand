<?php

namespace Ss\Designer\Controller\View;

use \Magento\Framework\App\Action\Action;

/**
 * Detail Designer page
 */
class Index extends Action
{

    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;
    protected $categoryRepository;
    protected $_catalogSession;
    protected $_registry;
    protected $_storeManager;
    protected $_designerFactory;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
                                \Magento\Framework\Registry $registry,
                                \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
                                \Magento\Catalog\Model\Session $catalogSession,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Ss\Designer\Model\DesignerFactory $designerFactory
    )
    {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->categoryRepository = $categoryRepository;
        $this->_catalogSession = $catalogSession;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_storeManager = $storeManager;
        $this->_designerFactory = $designerFactory;
    }

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $designerId = $this->getRequest()->getParam('designer_id', $this->getRequest()->getParam('id', false));
        $designer = $this->_designerFactory->create()->load($designerId);

        $designerTitle = $designer->getMetaTitle() ? $designer->getMetaTitle() : \Ss\Designer\Model\Designer::PREFIX_TITLE_DESIGNER . $designer->getName() . \Ss\Designer\Model\Designer::SUFFIX_TITLE_DESIGNER;
        $designerKeywords = $designer->getMetaKeywords();
        $designerDescription = $designer->getMetaDescription();
        
        if (!$designer->getIsActive()) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        // Add default param for layer navigation.
        $this->_initCategory($designerTitle, $designerKeywords, $designerDescription);
        $this->getRequest()->setParams([\Ss\Designer\Model\Designer::ATTRIBUTE_CODE => $designerId]);
        $this->getRequest()->setParam(\Ss\Theme\Helper\Data::IS_FILTER_DESIGNER_PAGE, TRUE);

        $designerHelper = $this->_objectManager->get('Ss\Designer\Helper\Designer');
        $resultPage = $designerHelper->prepareResultDesigner($this, $designerId);
        if (!$resultPage) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        return $resultPage;
    }

    /**
     * Initialize requested category object
     *
     * @return \Magento\Catalog\Model\Category
     */
    protected function _initCategory($titleDesigner, $keywordsDesigner, $descriptionDesigner)
    {
        $themeHelper = $this->_objectManager->create('Ss\Theme\Helper\Data');
        $categoryId = (int) $this->getRequest()->getParam(\Ss\Designer\Model\Designer::PARAM_FILTER_CATEGORY, false);
        if (!$categoryId) {
            // Set root category from config.
            $categoryId = $themeHelper->getConfigDesignerCategory();
        }

        try {
            $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        // Change meta data.
        if (!empty($titleDesigner)) {
            $category->setMetaTitle($titleDesigner);
        }
        
        if (!empty($keywordsDesigner)) {
            $category->setMetaKeywords($keywordsDesigner);
        }
        
        if (!empty($descriptionDesigner)) {
            $category->setMetaDescription($descriptionDesigner);
        }
        
        // Save category to session.
        $this->_catalogSession->setLastVisitedCategoryId($category->getId());
        $this->_registry->register('current_category', $category);

        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after', ['category' => $category, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return false;
        }

        return $category;
    }

}

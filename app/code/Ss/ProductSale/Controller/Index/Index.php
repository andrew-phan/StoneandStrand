<?php

namespace Ss\ProductSale\Controller\Index;

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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->categoryRepository = $categoryRepository;
        $this->_catalogSession = $catalogSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        // Add default param for layer navigation.
        $this->_initCategory(\Ss\ProductSale\Helper\Data::TITLE_PAGE_NEW);
        $this->getRequest()->setParam(\Ss\ProductSale\Helper\Data::PARAM_IS_SALE_PAGE, TRUE);        
        $this->getRequest()->setParam(\Ss\ProductSale\Helper\Data::PRODUCT_SALE_FILTER, \Ss\ProductSale\Helper\Data::DEFAULT_VALUE_FILTER_SALE);
        $this->getRequest()->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, \Ss\ProductSale\Helper\Data::DEFAULT_PATH_ALIAS);

        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    /**
     * Initialize requested category object
     *
     * @return \Magento\Catalog\Model\Category
     */
    protected function _initCategory($titleDesigner = '')
    {
        $themeHelper = $this->_objectManager->create('Ss\Theme\Helper\Data');
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            // Set root category from config.
            $categoryId = $themeHelper->getConfigDesignerCategory();
        }

        try {
            $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        // Change meta title.
        if (!empty($titleDesigner)) {
            $category->setMetaTitle($titleDesigner);
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

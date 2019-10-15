<?php

namespace Ss\Designer\Controller;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

/**
 * Set router to detail designer page
 */
class Router implements \Magento\Framework\App\RouterInterface
{

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Post factory
     *
     * @var \Ashsmith\Blog\Model\PostFactory
     */
    protected $_designerFactory;
    protected $_collectionFactory;
    protected $_urlFinderInterface;
    protected $_responseFactory;
    protected $_urlBuider;
    protected $_request;

    /**
     *
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Ss\Designer\Model\DesignerFactory $designerFactory
     */
    public function __construct(
    \Magento\Framework\App\ActionFactory $actionFactory,
        \Ss\Designer\Model\DesignerFactory $designerFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinderInterface,
        \Magento\Framework\App\Action\Context $contextRequest,
        \Magento\Framework\App\Helper\Context $contextHelper
    )
    {
        $this->actionFactory = $actionFactory;
        $this->_designerFactory = $designerFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_urlFinderInterface = $urlFinderInterface;
        $this->_urlBuider = $contextHelper->getUrlBuilder();
        $this->_request = $contextRequest->getRequest();
    }

    /**
     * Validate and Match Blog Post and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        // Should check again after upgrade Magento to 2.1 to ensure it's fixed by Magento
        $urlKey = $request->getRequestUri();
        
        if ($urlKey[0] == '/') {
            $urlKey = substr($urlKey, 1);
        }
        if ($urlKey[strlen($urlKey) - 1] == '/') {
            $urlKey = substr($urlKey, 0, -1);
        }

        $select = [
            'request_path' => $urlKey,
        ];
        $checkExistUrl = $this->_urlFinderInterface->findOneByData($select);
        if (!$checkExistUrl) {
            return null;
        }

        $entityType = $checkExistUrl->getEntityType();
        $targetPath = $checkExistUrl->getTargetPath();
        $urlExplode = explode('/', $targetPath);
        $isCustomLink = FALSE;
        if ($entityType == ProductUrlRewriteGenerator::ENTITY_TYPE) {
            // If this url for product
            if (strpos($targetPath, 'catalog/product/view/id/') !== false) {
                $productId = $urlExplode[4];
                $request->setModuleName('catalog')
                    ->setControllerName('product')
                    ->setActionName('view')
                    ->setParam('id', $productId);
                $request->setRequestUri($request->getPathInfo());
            } else {
                // Is custom link.
                $isCustomLink = TRUE;
            }
        } elseif ($entityType == \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite::ENTITY_TYPE_CUSTOM) {
            // Check if this url is designer page.
            if (strpos($targetPath, \Ss\Designer\Model\Designer::PREFIX_TARGET_PATH) !== false) {
                $designerId = $urlExplode[4];
                $request = $this->setRequestDesigner($request, $designerId);
                $request->setRequestUri($request->getPathInfo());
            } else {
                // Is custom link.
                $isCustomLink = TRUE;
            }
        }

        if ($isCustomLink) {
            $this->setCustomLink($targetPath);
        }

        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }

    /**
     * @todo To get product id by url_key
     * @param type $urlKey
     * @return type
     */
    public function getProductByUrlKey($urlKey)
    {
        $result = $this->_collectionFactory->create()
            ->addAttributeToFilter(\Ss\Theme\Helper\Attributes::URL_KEY, $urlKey)
            ->addAttributeToSelect(array(\Ss\Designer\Model\Designer::ATTRIBUTE_CODE))
            ->setPageSize(1)
        ;
        $data = '';
        foreach ($result->getItems() as $item) {
            $data = $item;
        }

        if (!empty($data)) {
            return $data;
        }

        return NULL;
    }

    /**
     * @todo to Set reqeust Designer redirect
     * @param type $request
     * @param type $designerId
     * @return type
     */
    public function setRequestDesigner($request,
        $designerId)
    {
        $designer = $this->_designerFactory->create();
        $designer->load($designerId);
        if ($designer) {

            // Redirect to detail designer page.
            $request->setModuleName(\Ss\Designer\Model\Designer::PREFIX_URL_DESIGNER)
                ->setControllerName('view')
                ->setActionName('index')
                ->setParam('designer_id', $designerId);
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $designer->getUrlPath());
            return $request;
        }

        return NULL;
    }

    /**
     * @todo set custom link
     * @param type $targetPath
     */
    public function setCustomLink($targetPath)
    {
        $baseUrl = $this->_urlBuider
            ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_WEB, '_secure' => $this->_request->isSecure()]);
        $urlRedirect = $baseUrl . $targetPath;

        header("Location: " . $urlRedirect);
        exit;
    }

}

<?php

namespace Magefan\Blog\Plugin\Helper\Wysiwyg;

class Images
{

    protected $_backendSession;
    protected $_storeManager;

    public function __construct(\Magento\Backend\Model\Session $backendSession, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_backendSession = $backendSession;
        $this->_storeManager = $storeManager;
    }

    public function aroundGetImageHtmlDeclaration(\Magento\Cms\Helper\Wysiwyg\Images $image,
                                                  \Closure $proceed,
                                                  $filename,
                                                  $renderAsTag = false)
    {
        $isSsWidgetImage = $this->_backendSession->getData('ss-widget-image');
        if (!$isSsWidgetImage) {
            $returnValue = $proceed($filename, $renderAsTag);
        } else {
            $fileurl = $image->getCurrentUrl() . $filename;
            $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $returnValue = str_replace($mediaUrl, '', $fileurl);
        }

        return $returnValue;
    }

}

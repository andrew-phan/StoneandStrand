<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Slideshow\Block\Widget;

/**
 * Banner Widget Block
 *
 */
class Slideshow extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate($this->getTemplateName());

    }

    /**
     * Get Title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');

    }

    /**
     * getBannerType
     * @return string
     */
    public function getType()
    {
        return $this->getData('banner_type');

    }

    /**
     * Get templateName
     */
    public function getTemplateName()
    {
        return !empty($this->getData('template')) ? $this->getData('template') : 'widget/slideshow.phtml';

    }

    /**
     * @todo To get list banner id from widget.
     * @return type
     */
    public function getBannerIds()
    {
        $listIds = explode(',', $this->getData('banner_ids'));

        return $listIds;

    }

    /**
     * Get post Ids
     * @return string
     */
    public function getPostIds()
    {
        $listIds = explode(',', $this->getData('post_ids'));

        return $listIds;

    }

    /**
     * @todo To get banner collection.
     * @return type
     */
    public function getCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $bannerCollection = $objectManager->create('Ss\Slideshow\Model\ResourceModel\Banner\Collection');
        $type = $this->getType() ? $this->getType() : 'homepage';
        $listIds = $this->getBannerIds();
        $bannerCollection->addBannerTypeFilter($type);
        $bannerCollection->addBannerIdsFilter($listIds);

        return $bannerCollection;

    }

    /**
     * @todo To get media Url.
     * @return type
     */
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

    }
}

<?php

namespace Ss\Slideshow\Model\ResourceModel\Banner;

/**
 * Subscription Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ss\Slideshow\Model\Banner', 'Ss\Slideshow\Model\ResourceModel\Banner');

    }

    /**
     * Add filter by banners
     *
     * @param array $bannerIds
     * @param bool $exclude
     * @return $this
     */
    public function addBannerIdsFilter($bannerIds, $exclude = false)
    {
        if (!empty($bannerIds)) {
            $this->addFieldToFilter('main_table.banner_id', [$exclude ? 'nin' : 'in' => $bannerIds]);
        }
        return $this;

    }

    /**
     * Add filter by banners
     *
     * @param array $bannerIds
     * @param bool $exclude
     * @return $this
     */
    public function addBannerTypeFilter($bannerType = 'homepage')
    {
        if (!empty($bannerType)) {
            $this->addFieldToFilter('main_table.banner_type', $bannerType);
        }
        return $this;

    }
}

<?php

/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */

namespace Ss\ProductSale\Helper;

/**
 * Designer helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const DEFAULT_PATH_ALIAS = "sale";      // Default alias page
    const TITLE_PAGE_NEW = "Product Sale";       // Title page product new
    const PRODUCT_SALE_FILTER    = 'special_price';   // Param for product sale filter
    const DEFAULT_VALUE_FILTER_SALE = "1-";     // Default value filter sale, special_price > 1
    const PARAM_IS_SALE_PAGE     = 'is_sale_page';  // param is sale page product    

}

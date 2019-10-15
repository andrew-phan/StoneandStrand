<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Shopby\Plugin;


class PagerPlugin
{
    protected $helper;

    public function __construct(\Amasty\Shopby\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    public function aroundGetPagerUrl(\Magento\Theme\Block\Html\Pager $subject, \Closure $closure, $params = [])
    {
        if($this->helper->isAjaxEnabled()) {
            $params['isAjax'] = null;
            $params['_'] = null;
        }
        
        $paramsRequest = $subject->getRequest()->getParams();
        unset($paramsRequest['p']);
        
        $params = array_merge($params, $paramsRequest);
        unset($params['id']);
        unset($params['ss_designer']);
        unset($params['is_filter_designer']);
        unset($params['designer_id']);

        return $closure($params);
    }
}

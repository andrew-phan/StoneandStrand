<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Sales\Block\Order\Email\Items\Order;

/**
 * Sales Order Email items default renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class DefaultOrder extends \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder
{    
    const OPTION_DEFAULT = 'default'; // key option default
    const OPTION_EXTENSION = 'extension'; // key option extension

    /**
     * @return array
     */
    public function getItemOptions()
    {
        $default = [];
        $extension = [];
        $options = $this->getItem()->getProductOptions();
        
        if ($options) {
            if (isset($options['options'])) {
                $extension = array_merge($extension, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $default = array_merge($default, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $default = array_merge($default, $options['attributes_info']);
            }
        }
        
        $result = array_filter([static::OPTION_DEFAULT => $default, static::OPTION_EXTENSION => $extension]);
        
        return $result;
    }

}

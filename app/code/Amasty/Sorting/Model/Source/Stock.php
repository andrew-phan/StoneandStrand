<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Source;

/**
 * Class Stock
 *
 * @package Amasty\Sorting\Model\Source
 */
class Stock
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => 0,
                'label' => __('No')
            ],
            [
                'value' => 1,
                'label' => __('Yes')],
            [
                'value' => 2,
                'label' => __('Yes for Catalog, No for Search')
            ]
        ];

        return $options;
    }
}
<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Model\Source;

/**
 * Class Price
 *
 * @package Amasty\Sorting\Model\Source
 */
class Price
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => 'min_price',
                'label' => __('Minimal Price'),
            ),
            array(
                'value' => 'price',
                'label' => __('Price'),
            ),
            array(
                'value' => 'final_price',
                'label' => __('Final Price'),
            ),
        );

        return $options;
    }
}
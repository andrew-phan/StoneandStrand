<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Api;

/**
 * Interface IndexedMethodInterface
 * @api
 */
interface MethodInterface
{
    public function apply($collection, $direction);
}
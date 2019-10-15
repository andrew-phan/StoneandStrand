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
interface IndexedMethodInterface
{
    public function getIndexTable();

    public function getColumnSelect();

    public function getIndexSelect();

    public function reindex();
}
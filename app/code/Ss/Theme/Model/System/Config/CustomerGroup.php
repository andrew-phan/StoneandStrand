<?php

/**
 * Magestore.
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
 * @category    Magestore
 * @package     Magestore_Megamenu
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Ss\Theme\Model\System\Config;

/**
 * Class list customer group
 */
class CustomerGroup
{

    /**
     * @todo to get list customer group
     * @return type
     */
    public function toOptionArray()
    {
        $results = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerGroup = $objectManager->create("\Magento\Customer\Model\Group")->getCollection();
        foreach ($customerGroup as $item) {
            $customGroupId = $item->getCustomerGroupId();
            if ($customGroupId == 0 || $customGroupId == 1) {
                continue;
            }
            $results[$customGroupId] = $item->getCustomerGroupCode();
        }

        return $results;
    }

}

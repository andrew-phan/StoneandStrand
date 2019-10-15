<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\SendFriend\Block;

/**
 * Email to a Friend Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Send extends \Magento\SendFriend\Block\Send
{

    public function beforeToHtml(\Magento\SendFriend\Block\Send $originalBlock)
    {
        $originalBlock->setTemplate('Ss_SendFriend::send.phtml');

    }
}

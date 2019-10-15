<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\SendFriend\Controller\Product;

use Magento\Framework\Controller\ResultFactory;

class Send extends \Magento\SendFriend\Controller\Product\Send
{
    /**
     * Show Send to a Friend Form
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $product = $this->_initProduct();

        if (!$product) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }

        if ($this->sendFriend->getMaxSendsToFriend() && $this->sendFriend->isExceedLimit()) {
            $this->messageManager->addNotice(
                __('You can\'t send messages more than %1 times an hour.', $this->sendFriend->getMaxSendsToFriend())
            );
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->_eventManager->dispatch('sendfriend_product', ['product' => $product]);

        $data = $this->catalogSession->getSendfriendFormData();

        $block = $resultPage->getLayout()->getBlock('sendfriend.send');
        if ($data) {
            $this->catalogSession->setSendfriendFormData(true);
            if ($block) {
            /** @var \Magento\SendFriend\Block\Send $block */
                $block->setFormData($data);
            }
        }

        $response = $block->toHtml();
        $this->getResponse()->setBody($response);
        return;

    }
}

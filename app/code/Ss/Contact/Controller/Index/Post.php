<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Contact\Controller\Index;

class Post extends \Magento\Contact\Controller\Index
{
    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $redirect = 'contact-us';
        
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect($redirect);
            return;
        }

        $this->inlineTranslation->suspend();
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);

            $error = false;
            
            if ( ! \Zend_Validate::is(trim($post['name']), 'NotEmpty')
                || ! \Zend_Validate::is(trim($post['comment']), 'NotEmpty')
                || ! \Zend_Validate::is(trim($post['email']), 'EmailAddress')
                || \Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                $error = true;
            }
            
            if( ! empty($post['order-number']) && ! $this->_haveOrder($post['order-number'])) {
                $error = true;
                $this->messageManager->addError(
                    __('Order not exist so we can\'t process your request right now.')
                );
            }
            
            if (! $error) {
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                    ->addTo($this->scopeConfig->getValue(\Ss\Theme\Helper\Data::XML_PATH_EMAIL_GENERAL, $storeScope))
                    ->setReplyTo($post['email'])
                    ->getTransport();

                $transport->sendMessage();
                $this->messageManager->addSuccess(
                    __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
                );
                $redirect = '';
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
        }
        
        $this->inlineTranslation->resume();
        
        $this->_redirect($redirect);
        return;
    }
    
    /**
     * Check order have exist by order increment id.
     * 
     * @param type $orderNumber
     * @return boolean
     */
    private function _haveOrder($orderNumber)
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($orderNumber)->toArray();

        if ($order) {
            return true;
        }
        
        return false;
    }
}

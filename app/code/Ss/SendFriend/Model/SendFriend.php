<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\SendFriend\Model;

use Magento\Framework\Exception\LocalizedException as CoreException;

/**
 * SendFriend Log
 *
 * @method \Magento\SendFriend\Model\ResourceModel\SendFriend _getResource()
 * @method \Magento\SendFriend\Model\ResourceModel\SendFriend getResource()
 * @method int getIp()
 * @method \Magento\SendFriend\Model\SendFriend setIp(int $value)
 * @method int getTime()
 * @method \Magento\SendFriend\Model\SendFriend setTime(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SendFriend extends \Magento\SendFriend\Model\SendFriend
{

    protected $_designerHelper;

    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Ss\Designer\Helper\Data $designerHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Catalog\Helper\Image $catalogImage, \Magento\SendFriend\Helper\Data $sendfriendData, \Magento\Framework\Escaper $escaper, \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress, \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
    )
    {
        parent::__construct($context, $registry, $storeManager, $transportBuilder, $catalogImage, $sendfriendData, $escaper, $remoteAddress, $cookieManager, $inlineTranslation, $resource, $resourceCollection, $data);
        $this->_designerHelper = $designerHelper;
    }

    /**
     * @return $this
     * @throws CoreException
     */
    public function send()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $designerId = $this->getProduct()->getSsDesigner();
        $designer = '';
        if($designerId){
            $designer = $this->_designerHelper->getDesignerById($designerId);
        }

        $this->inlineTranslation->suspend();
        $subject = nl2br(htmlspecialchars($this->getSender()->getSubject()));
        $message = nl2br(htmlspecialchars($this->getSender()->getMessage()));
        $sender = [
            'name' => $this->_escaper->escapeHtml($this->getSender()->getName()),
            'email' => $this->_escaper->escapeHtml($this->getSender()->getEmail()),
        ];

        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $themeHelper = $objectManager->create('\Ss\Theme\Helper\Data');

        if ($customerSession->isLoggedIn()) {
            $sender['first_name'] = $customerSession->getCustomer()->getFirstname();
        } else {
            $sender['first_name'] = $this->_escaper->escapeHtml($this->getSender()->getName());
        }

        $sendtos = $this->_escaper->escapeHtml($this->getSender()->getSendto());
        $sendtos = explode(",", $sendtos);

        $media = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $senthint_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SEND_HINT_YOU_BEEN_SENT, 'identifier');
        $aboutus_post = $objectManager->create('Magefan\Blog\Model\Post')->load(\Ss\Theme\Helper\EmailTemplate::SEND_HINT_ABOUT_US, 'identifier');
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
        foreach ($sendtos as $sendto) {
            $sendto = trim($sendto);
            $this->_transportBuilder->setTemplateIdentifier(
                $this->_sendfriendData->getEmailTemplate()
            )->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )->setFrom(
                $sender
            )->setTemplateVars(
                [
                    'email' => $sendto,
                    'product_name' => $this->getProduct()->getName(),
                    'product_price' => $priceHelper->currency($this->getProduct()->getFinalPrice(), true, false),
                    'product_url' => $this->getProduct()->getUrlInStore(),
                    'subject' => $subject,
                    'message' => $message,
                    'sender_name' => $sender['first_name'],
                    'sender_email' => $sender['email'],
                    'product_image' => $this->_catalogImage->init($this->getProduct(), 'sendfriend_small_image')
                        ->resize(280)->getUrl(),
                    'designer_name' => ($designer) ? $designer->getName() : '',
                    'email_config' => $themeHelper->getEmailToLink(),
                    'telephone_config' => $themeHelper->getTelephone(),
                    'Tribeca_Url' => $themeHelper->getTribecaUrl(),
                    'aboutus_content' => html_entity_decode(strip_tags($aboutus_post->getContent())),
                    'aboutus_image' => $media . $aboutus_post->getFeaturedImg(),
                    'senthint_title' => $senthint_post->getTitle(),
                    'senthint_content' => html_entity_decode(strip_tags($senthint_post->getContent())),

                ]
            )->addTo(
                $sendto
            );
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        }

        $this->inlineTranslation->resume();

        $this->_incrementSentCount();

        return $this;
    }

    public function getSentoEmails()
    {
        $sendtos = $this->_escaper->escapeHtml($this->getSender()->getSendto());
        $sendtos = explode(",", $sendtos);
        return $sendtos;
    }

    /**
     * Validate Form data
     *
     * @return bool|string[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate()
    {
        $errors = [];

        $name = $this->getSender()->getName();
        if (empty($name)) {
            $errors[] = __('Please enter a sender name.');
        }

        $email = $this->getSender()->getEmail();
        if (empty($email) || !\Zend_Validate::is($email, 'EmailAddress')) {
            $errors[] = __('Invalid Sender Email');
        }

        foreach ($this->getSentoEmails() as $email) {
            $email = trim($email);
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                $errors[] = __('Please enter a correct recipient email address.');
                break;
            }
        }

        $maxRecipients = $this->getMaxRecipients();
        if (count($this->getSentoEmails()) > $maxRecipients) {
            $errors[] = __('No more than %1 emails can be sent at a time.', $this->getMaxRecipients());
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

}

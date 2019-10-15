<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Rma\Block\Widget;

/**
 * Banner Widget Block
 *
 */
class Post extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;
    
    /**
     * @var \Magento\Framework\Filter\Template
     */
    protected $_filterTemplate;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param \Magento\Framework\Filter\Template $filterTemplate
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Filter\Template $filterTemplate,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = [])
    {
        $this->_filterTemplate = $filterTemplate;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
        
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate($this->getTemplateName());

    }

    /**
     * @return \Magento\Framework\Filter\Template
     */
    public function getFilterTemplate()
    {
        return $this->_filterTemplate;

    }

    /**
     * Get Title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');

    }

    /**
     * Get templateName
     */
    public function getTemplateName()
    {
        return !empty($this->getData('template')) ? $this->getData('template') : 'widget/post.phtml';

    }

    /**
     * Get post Ids
     * @return string
     */
    public function getPostIds()
    {
        return $this->getData('post_ids');

    }

    /**
     * Get Post by Id
     * @param int $postId
     * @return object|boolean
     */
    public function getPostById($postId)
    {
        if (!isset($postId) || empty($postId)) {
            return false;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('Magefan\Blog\Model\Post')->load($postId);

    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}

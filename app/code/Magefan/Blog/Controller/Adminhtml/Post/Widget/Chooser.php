<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magefan\Blog\Controller\Adminhtml\Post\Widget;

class Chooser extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Chooser Source action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
//        $uniqId = $this->getRequest()->getParam('uniq_id');
//        $massAction = $this->getRequest()->getParam('use_massaction', false);
//
//        $layout = $this->layoutFactory->create();
//        $productsGrid = $layout->createBlock(
//            'Magefan\Blog\Block\Adminhtml\Post\Widget\Chooser',
//            '',
//            [
//                'data' => [
//                    'id' => $uniqId,
//                    'use_massaction' => $massAction,
//                ]
//            ]
//        );
//
//        $html = $productsGrid->toHtml();
//
//        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
//        $resultRaw = $this->resultRawFactory->create();
//        return $resultRaw->setContents($html);

        $bannersGrid = $this->_view->getLayout()->createBlock(
            'Magefan\Blog\Block\Adminhtml\Post\Widget\Chooser',
            '',
            ['data' => []]
        );
        $html = $bannersGrid->toHtml();

        $this->getResponse()->setBody($html);
    }
    
    
}

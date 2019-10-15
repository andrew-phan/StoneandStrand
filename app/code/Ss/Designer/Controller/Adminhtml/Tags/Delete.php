<?php

namespace Ss\Designer\Controller\Adminhtml\Tags;

/**
 * Delete Banner action
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Delete extends \Ss\Designer\Controller\Adminhtml\Tags
{

    public function execute()
    {
        $tagId = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
        try {
            $banner = $this->_objectManager->create('Ss\Designer\Model\Tags')->setId($tagId);
            $banner->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }

}

<?php

namespace Ss\Designer\Controller\Adminhtml\Designer;

/**
 * Delete Designer.
 */
class Delete extends \Ss\Designer\Controller\Adminhtml\Designer
{

    /**
     * 
     * @return type
     */
    public function execute()
    {
        $designerId = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
        try {
            $banner = $this->_objectManager->create('Ss\Designer\Model\Designer')->setId($designerId);
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

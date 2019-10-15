<?php

namespace Ss\Collection\Controller\Adminhtml\Collection;

/**
 * Delete Collection.
 */
class Delete extends \Ss\Collection\Controller\Adminhtml\Collection
{

    /**
     * 
     * @return type
     */
    public function execute()
    {
        $collectionId = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
        try {
            $banner = $this->_objectManager->create('Ss\Collection\Model\Collection')->setId($collectionId);
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

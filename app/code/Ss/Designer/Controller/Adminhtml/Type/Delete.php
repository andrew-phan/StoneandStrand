<?php

namespace Ss\Designer\Controller\Adminhtml\Type;

class Delete extends \Ss\Designer\Controller\Adminhtml\Type
{

    public function execute()
    {
        $typeId = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
        try {
            $type = $this->_objectManager->create('Ss\Designer\Model\Type')->setId($typeId);
            $type->delete();
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

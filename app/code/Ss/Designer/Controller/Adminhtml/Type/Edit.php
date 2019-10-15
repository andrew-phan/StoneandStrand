<?php

namespace Ss\Designer\Controller\Adminhtml\Type;

class Edit extends \Ss\Designer\Controller\Adminhtml\Type
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('type_id');
        $model = $this->_objectManager->create('Ss\Designer\Model\Type');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This type no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('designer_type', $model);

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }

}

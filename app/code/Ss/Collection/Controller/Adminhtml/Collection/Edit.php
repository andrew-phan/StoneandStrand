<?php

namespace Ss\Collection\Controller\Adminhtml\Collection;

/**
 * Edit Banner action.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Edit extends \Ss\Collection\Controller\Adminhtml\Collection
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('collection_id');
        $model = $this->_objectManager->create('Ss\Collection\Model\Collection');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This collection no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('collection', $model);

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }

}

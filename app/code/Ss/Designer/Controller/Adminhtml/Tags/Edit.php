<?php

namespace Ss\Designer\Controller\Adminhtml\Tags;

/**
 * Edit Banner action.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Edit extends \Ss\Designer\Controller\Adminhtml\Tags
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('tag_id');
        $model = $this->_objectManager->create('Ss\Designer\Model\Tags');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This tag no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('designer_tag', $model);

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }

}

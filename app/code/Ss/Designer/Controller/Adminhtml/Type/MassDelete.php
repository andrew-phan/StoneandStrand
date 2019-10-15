<?php

namespace Ss\Designer\Controller\Adminhtml\Type;

class MassDelete extends \Ss\Designer\Controller\Adminhtml\Type
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $typeIds = $this->getRequest()->getParam('type');
        if (!is_array($typeIds) || empty($typeIds)) {
            $this->messageManager->addError(__('Please select type(s).'));
        } else {
            $bannerCollection = $this->_objectManager->create('Ss\Designer\Model\ResourceModel\Type\Collection')
                ->addFieldToFilter('type_id', ['in' => $typeIds]);
            try {
                foreach ($bannerCollection as $banner) {
                    $banner->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($typeIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }

}

<?php

namespace Ss\Designer\Controller\Adminhtml\Designer;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Save Designer.
 */
class Save extends \Ss\Designer\Controller\Adminhtml\Designer
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $isChangeUrlKey = 1;
            $isChangeType = 1;
            $oldUrlKey = '';
            $isNew = 1;
            $isBackupUrl = 0;
            $model = $this->_objectManager->create('Ss\Designer\Model\Designer');

            if ($id = $this->getRequest()->getParam(static::PARAM_CRUD_ID)) {
                $model->load($id);
                $isNew = 0;
                $oldUrlKey = $model->getUrlPath();
                $isChangeUrlKey = ($model->getUrlKey() != $data["url_key"]) ? 1 : 0;
                $isChangeType =  ($model->getTypeId() != $data["type_id"]) ? 1 : 0;
            }
            
            if($isChangeUrlKey || $isChangeType){
                $isBackupUrl = $data["is_backup"];
            }

            $data = $this->uploadImage('image', $data);
            $data = $this->uploadImage('image_product', $data);

            if (isset($data["designer_tags"])) {
                $tagsData = $this->_jsHelper->decodeGridSerializedInput($data["designer_tags"]);
                $data['param_tag_ids'] = $tagsData;
                unset($data['designer_tags']);
            }

            $model->setData($data);
            $model->setIsNew($isNew);
            $model->setOldUrlKey($oldUrlKey);
            $model->setData(\Ss\Designer\Model\Designer::PARAM_CHANGE_URL_KEY, $isChangeUrlKey);
            $model->setData(\Ss\Designer\Model\Designer::PARAM_CHANGE_TYPE, $isChangeType);
            $model->setData(\Ss\Designer\Model\Designer::PARAM_BACKUP_URL, $isBackupUrl);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The designer has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getBackResultRedirect($resultRedirect, $model->getId());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the designer.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                    '*/*/edit', [static::PARAM_CRUD_ID => $this->getRequest()->getParam(static::PARAM_CRUD_ID)]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

    public function uploadImage($idImage,
        $data)
    {
        $imageRequest = $this->getRequest()->getFiles($idImage);
        if ($imageRequest) {
            if (isset($imageRequest['name'])) {
                $fileName = $imageRequest['name'];
            } else {
                $fileName = '';
            }
        } else {
            $fileName = '';
        }

        if ($imageRequest && strlen($fileName)) {
            /*
             * Save image upload
             */
            try {
                $uploader = $this->_objectManager->create(
                    'Magento\MediaStorage\Model\File\Uploader', ['fileId' => $idImage]
                );
                $uploader->setAllowedExtensions(['jpg',
                    'jpeg',
                    'gif',
                    'png']);

                /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);

                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $result = $uploader->save(
                    $mediaDirectory->getAbsolutePath(\Ss\Designer\Model\Designer::BASE_MEDIA_PATH)
                );
                $data[$idImage] = \Ss\Designer\Model\Designer::BASE_MEDIA_PATH . $result['file'];
            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        } else {
            if (isset($data[$idImage]) && isset($data[$idImage]['value'])) {
                if (isset($data[$idImage]['delete'])) {
                    $data[$idImage] = null;
                    $data['delete_' . $idImage] = true;
                } elseif (isset($data[$idImage]['value'])) {
                    $data[$idImage] = $data[$idImage]['value'];
                } else {
                    $data[$idImage] = null;
                }
            }
        }

        return $data;
    }

}

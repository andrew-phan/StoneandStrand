<?php
 
namespace CustomerParadigm\MaintenanceMode\Controller\Adminhtml\AdjustSettings;

use Magento\Backend\App\Action;
 
class Save extends \Magento\Backend\App\Action
{
	
	protected $_helper;
	
    /**
     * @param Action\Context $context
     */
    public function __construct(
		Action\Context $context,
		\CustomerParadigm\MaintenanceMode\Helper\Data $helper
	)
    {
        parent::__construct($context);
		$this->_helper = $helper;
    }
	
   /**
     * @return void
     */
   public function execute()
   {
      $isPost = $this->getRequest()->getPost();

      if ($isPost) {

		// set post variables
		$ison = $this->getRequest()->getParam('ison');
		$whitelist = $this->getRequest()->getParam('whitelist');
		$global_503error = $this->getRequest()->getParam('global503_error');
		$global_503css = $this->getRequest()->getParam('global503_css');

		// set new 503 message values via helper
		try {
			$this->_helper->set($ison);	
			$this->_helper->setAddresses($whitelist);
//			$this->_helper->setErrorHtml($global_503error);
//			$this->_helper->setErrorCss($global_503css);
			$this->messageManager->addSuccess(__('You have successfully saved the Maintenance Mode settings.'));
			// forward back to edit form
			$this->_forward('edit');
		} catch (\Exception $e) {
			$this->messageManager->addError(__(
				'There was an error in saving the Maintenance Mode settings as follows:  "' . 
				$e->getMessage() . 
				'".  Please try again.'));
			// forward back to edit form
			$this->_forward('edit');
		}

      }
   }
   
    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CustomerParadigm_MaintenanceMode::manage503');
    }
}
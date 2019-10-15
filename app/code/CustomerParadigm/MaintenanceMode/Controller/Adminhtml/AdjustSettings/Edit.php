<?php
 
namespace CustomerParadigm\MaintenanceMode\Controller\Adminhtml\AdjustSettings;

use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\State;
 
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
		State $appState,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_appState = $appState;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
	
   /**
     * @return void
     */
   public function execute()
   { 
		// @var \Magento\Framework\App\State
 		$isDeveloperMode = $this->_appState->getMode() == State::MODE_DEVELOPER;
		// informs user the maintenance page will not display properly while in developer mode
		if ($isDeveloperMode) {
			$this->messageManager->addNotice(__(
				'Your site is currently in developer mode. Please note that the maintenance mode page will not display properly while in developer mode.'));
		}
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Maintenance Mode Settings'));
		
        return $resultPage;
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
 
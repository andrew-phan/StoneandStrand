<?php

namespace CustomerParadigm\MaintenanceMode\Controller\Adminhtml\AdjustSettings;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Index extends \Magento\Backend\App\Action
{
	
    /**
     * Index action
     *
     * @return void
     */
	public function execute()
	{
		$this->_forward('edit');
	}
}
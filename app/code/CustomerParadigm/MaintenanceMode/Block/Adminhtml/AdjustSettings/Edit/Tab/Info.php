<?php
 
namespace CustomerParadigm\MaintenanceMode\Block\Adminhtml\AdjustSettings\Edit\Tab;
 
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use CustomerParadigm\MaintenanceMode\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
 
class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    /**
     * @var \CustomerParadigm\MaintenanceMode\Helper\Data
     */	
	protected $_helper;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */	
	//protected $_scopeConfig;	
 
   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
	 * @param Data $helper
	 * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
		Data $helper,
		//ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
		$this->_helper = $helper;
		//$this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
 	
        // @var \Magento\Framework\Data\Form $form
        $form = $this->_formFactory->create();
		$legendNotes = 'Maintenance Mode Settings';
		$legendNotes .= '<br>';
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => $legendNotes]
        );
		
		// @var boolean $isOn
		$isOn = $this->_helper->isOn();
		// notes string
		$isOnNotes = '';
		
		if ($isOn) {
			$isOnNotes .= '<span style="color:red;">Notice:&nbsp;the site is currently in maintenance mode.</span>';
		}
		// adds isOn form field to toggle maintenance mode on/off
        $fieldset->addField(
            'ison',
            'select',
            [
                'name'        => 'ison',
                'label'    => __('Toggle Maintenance Mode'),
				'options' => ['1' => __('Enabled'), '0' => __('Disabled')],
				'value' => $isOn,
				'note' => $isOnNotes,
                'required'     => true
            ]
        );
		
		// @var string $whiteList
		$whiteList = $this->_helper->getWhiteList();
		// @var string $ip
		$ip = $_SERVER['REMOTE_ADDR'];
		// @var string $ipMessage
		$ipMessage = 'Please enter each IP address to be white listed separated by commas.';
		// checks if white list contains any records
		if ($whiteList) {
			// builds the "note" message for form field (informing user of addition of admin IP address)
			if (strpos($whiteList, $ip) !== false) {
			}
			else {
				// assigns current admin user ip to white list
				$whiteList .= ',' . $ip;
				$ipMessage .= '<br><br><span style="color:red;"><b>IMPORTANT NOTE:</b>  To maintain access to your admin panel, your current IP address "' . $ip . '" has been automatically added to the white list.</span>';
			}
		}
		// assign ip to empty $whiteList variable
		else {
			if ($ip) {
				$whiteList = $ip;
			}
		}
		
		// adds whitelist form field for managing maintenance mode allowed IP addresses
        $fieldset->addField(
            'whitelist',
            'textarea',
            [
                'name'      => 'whitelist',
                'label'     => __('White List IP Addresses'),
                'required'  => true,
				'value' => $whiteList,
				'note' 	=>  $ipMessage,
            ]
        );
		// @var array[] $wysiwygData
		$wysiwygData = array();
		// @var string $cssUrl
		$cssUrl = $this->getBaseUrl();
		// @var string $cssUrl
		$cssUrl .= 'pub/errors/maintenancemode/css/styles.css';
		// @var array[] $wysiwygData
		$wysiwygData['content_css'] = $cssUrl;
		// @var array[] $wysiwygData
		$wysiwygData['add_variables'] = false;
		// @var array[] $wysiwygData
		$wysiwygData['use_container'] = true;
		// @var array[] $wysiwygData
		$wysiwygData['encode_directives'] = false;
		// @var array[] $wysiwygData
		$wysiwygData['add_widgets'] = false;
		// @var string $note
		$note = 'This is the HTML for your custom Maintenance Page.';
		// only allow images if "use_static_urls_in_catalog" is enabled
		if ($this->_scopeConfig->getValue('cms/wysiwyg/use_static_urls_in_catalog')) {
			// @var array[] $wysiwygData
			$wysiwygData['add_images'] = true;
		}
		// disable image insertion
		else {
			// @var array[] $wysiwygData
			$wysiwygData['add_images'] = false;
			// @var string $note
			//$note .= '<br><br>To add images to your custom maintenance page, you must first enable "Use Static URLs for Media Content in WYSIWYG for Catalog" option.  This can be edited by going to the admin menu located at Stores->Configuration->General->Content Management.';
		} 
        $wysiwygConfig = $this->_wysiwygConfig->getConfig($wysiwygData);
		// @var string $errorHtml
		$errorHtml = '';
		// @var string $errorHtml
		$errorHtml = $this->_helper->getErrorHtml();
		
		// adds global503_error form field - the Html for the custom error page
        $fieldset->addField(
            'global503_error',
            'hidden',
            [
                'name'        => 'global503_error',
                'label'    => __('Maintenance Error Message'),
                'required'     => true,
				'style' => 'height: 30em;',
				'value' => $errorHtml,
				'note' => $note,
                'config'    => $wysiwygConfig
            ]
        );
		// @var string $note
		$note = '(Advanced Users)  This is the CSS for your custom Maintenance Page.';
		$note .= '<br><br><b>IMPORTANT NOTE:</b>&nbsp;Any CSS changes made in the WYSIWYG editor above will take precendence over CSS changes made here.';
		$note .= '<br><br><b>INFORMATION:</b>';
		$note .= '<br><br>* If you are running this site using multiple frontend servers, this extension may not work properly on all instances.';
		$note .= '<br><br>** If you have not set an admin user white list IP address and enable Maintenance Mode, you will need to disable maintenance mode manually by deleting the file labeled ".maintenance.flag" located in the /var folder of your root Magento installation.';
		// @var string $cssData
		$cssData = '';
		// @var string $cssData
		$cssData = $this->_helper->getErrorCss();
		
		// adds global503_css form field - the CSS for the custom error page
        $fieldset->addField(
            'global503_css',
            'hidden',
            [
                'name'        => 'global503_css',
                'label'    => __('Maintenance Mode CSS Styling'),
                'required'     => true,
				'style' => 'height: 20em;',
				'value' => $cssData,
				'note' => $note
            ]
        );

        $this->setForm($form);
 
        return parent::_prepareForm();
    }
 
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Maintenance Mode');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Maintenance Mode');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
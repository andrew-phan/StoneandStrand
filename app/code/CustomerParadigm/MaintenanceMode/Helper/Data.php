<?php
namespace CustomerParadigm\MaintenanceMode\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $maintenanceMode;	
	protected $fileSystem;
	
    /**
     * Maintenance flag dir
     */
    const PUB = DirectoryList::PUB;
	
    /**
     * Path to store files
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $pubDir;
	
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param CustomerMetadataInterface $customerMetadataService
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Filesystem $fileSystem,
		\Magento\Framework\App\MaintenanceMode $maintenanceMode
    ) {
        parent::__construct($context);
		$this->maintenanceMode = $maintenanceMode;
		$this->fileSystem = $fileSystem;
		$this->pubDir = $this->fileSystem->getDirectoryWrite(self::PUB);
    }
	
    /**
     * Checks if maintenance flag is on
     *
     * @return boolean
     */
	public function isOn() {
		return $this->maintenanceMode->isOn();
	}

	/**
     * Toggles maintenance mode to $ison value
	 *
     * @param boolean $ison
     * @return boolean
     */
	public function set($ison) {
		return $this->maintenanceMode->set($ison);
	}

	/**
     * Sets white list addresses equal to $whitelist
     *
	 * @param string $whitelist
     * @return boolean
     */	
	public function setAddresses($whitelist) {
		$whitelist = preg_replace('/\s+/', '', $whitelist);
		return $this->maintenanceMode->setAddresses($whitelist);
	}

	/**
     * Returns string of white list IP addresses
     *
     * @return string
     */
	public function getWhiteList() {
		$whiteList = $this->maintenanceMode->getAddressInfo();
		if ($whiteList) {
			return implode (', ', $whiteList);
		}
		return NULL;
	}

	/**
     * Returns contents of 503.pthml
     *
     * @return string
     */
	public function getErrorHtml() {
		
		$errorHtml = '';

		$fileHandler = $this->fileSystem->
			getDirectoryRead(DirectoryList::PUB)->getAbsolutePath('errors/default/302.phtml');
		if ($fileHandler) {
			$errorHtml = file_get_contents ($fileHandler);
		}
		return $errorHtml;
	}
	
	/**
     * Sets value of 503.pthml file
     *
	 * @param string
     * @return boolean
     */
	public function setErrorHtml($errorHtml) {
		$message = '';
		if ($errorHtml) {
			$fileHandler = $this->fileSystem->
			getDirectoryRead(DirectoryList::PUB)->getAbsolutePath('errors/default/302.phtml');
			if ($fileHandler) {
				$message = file_put_contents ($fileHandler, $errorHtml);
			}
		}
		return $message;				
	}

	/**
     * Gets contents of styles.css file
     *
     * @return string
     */
	public function getErrorCss() {
		$cssContent = '';
		$fileHandler = $this->fileSystem->
			getDirectoryRead(DirectoryList::PUB)->getAbsolutePath('errors/default/css/styles.css');
		if ($fileHandler) {
			$cssContent = file_get_contents ($fileHandler);
		}
		return $cssContent;
	}

	/**
     * Sets content of styles.css file
     *
     * @return boolean
     */	
	public function setErrorCss($cssContent) {
		$message = '';
		if ($cssContent) {
			$fileHandler = $this->fileSystem->
				getDirectoryRead(DirectoryList::PUB)->getAbsolutePath('errors/default/css/styles.css');
			if ($fileHandler) {
				$message = file_put_contents ($fileHandler, $cssContent);
			}
		}
		return $message;
	}
}
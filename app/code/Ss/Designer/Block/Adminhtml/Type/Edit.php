<?php

/**
 * 
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Bannerslider
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Ss\Designer\Block\Adminhtml\Type;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'type_id';
        $this->_blockGroup = 'Ss_Designer';
        $this->_controller = 'adminhtml_type';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Type'));
        $this->buttonList->update('delete', 'label', __('Delete'));

        if ($this->getRequest()->getParam('current_type_id')) {
            $this->buttonList->remove('save');
            $this->buttonList->remove('delete');

            $this->buttonList->remove('back');
            $this->buttonList->add(
                'close_window', [
                'label' => __('Close Window'),
                'onclick' => 'window.close();',
                ], 10
            );

            $this->buttonList->add(
                'save_and_continue', [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'onclick' => 'customsaveAndContinueEdit()',
                ], 10
            );

            $this->buttonList->add(
                'save_and_close', [
                'label' => __('Save and Close'),
                'class' => 'save_and_close',
                'onclick' => 'saveAndCloseWindow()',
                ], 10
            );

            $this->_formScripts[] = "
				require(['jquery'], function($){
					$(document).ready(function(){
						var input = $('<input class=\"custom-button-submit\" type=\"submit\" hidden=\"true\" />');
						$(edit_form).append(input);

						window.customsaveAndContinueEdit = function (){
							edit_form.action = '" . $this->getSaveAndContinueUrl() . "';
							$('.custom-button-submit').trigger('click');

				        }

			    		window.saveAndCloseWindow = function (){
			    			edit_form.action = '" . $this->getSaveAndCloseWindowUrl() . "';
							$('.custom-button-submit').trigger('click');
			            }
					});
				});
			";

            if ($bannerId = $this->getRequest()->getParam('type_id')) {
                $this->_formScripts[] = '
					window.type_id = ' . $bannerId . ';
				';
            }
        } else {
            $this->buttonList->add(
                'save_and_continue', [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ],
                ], 10
            );
        }

        if ($this->getRequest()->getParam('saveandclose')) {
            $this->_formScripts[] = 'window.close();';
        }
    }

    /**
     * Retrieve the save and continue edit Url.
     *
     * @return string
     */
    protected function getSaveAndContinueUrl()
    {
        return $this->getUrl(
                '*/*/save', [
                '_current' => true,
                'back' => 'edit',
                'tab' => '{{tab_id}}',
                'store' => $this->getRequest()->getParam('store'),
                'type_id' => $this->getRequest()->getParam('type_id'),
                'current_type_id' => $this->getRequest()->getParam('current_type_id'),
                ]
        );
    }

    /**
     * Retrieve the save and continue edit Url.
     *
     * @return string
     */
    protected function getSaveAndCloseWindowUrl()
    {
        return $this->getUrl(
                '*/*/save', [
                '_current' => true,
                'back' => 'edit',
                'tab' => '{{tab_id}}',
                'store' => $this->getRequest()->getParam('store'),
                'type_id' => $this->getRequest()->getParam('type_id'),
                'current_type_id' => $this->getRequest()->getParam('current_type_id'),
                'saveandclose' => 1,
                ]
        );
    }

}

<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Email\Model;

class Template extends \Magento\Email\Model\Template
{

    /**
     * Add variables that are used by transactional and newsletter emails
     *
     * @param array $variables
     * @param null|string|bool|int|Store $storeId
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addEmailVariables($variables, $storeId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $themeHelper = $objectManager->create("\Ss\Theme\Helper\Data");

        $variables['title_menu_1'] = $themeHelper->getTitleMenu1();
        $variables['link_menu_1'] = $themeHelper->getLinkMenu1();
        $variables['title_menu_2'] = $themeHelper->getTitleMenu2();
        $variables['link_menu_2'] = $themeHelper->getLinkMenu2();
        $variables['title_menu_3'] = $themeHelper->getTitleMenu3();
        $variables['link_menu_3'] = $themeHelper->getLinkMenu3();
        $variables['title_menu_4'] = $themeHelper->getTitleMenu4();
        $variables['link_menu_4'] = $themeHelper->getLinkMenu4();
        $variables['title_menu_5'] = $themeHelper->getTitleMenu5();
        $variables['link_menu_5'] = $themeHelper->getLinkMenu5();
        $variables['link_top_message'] = $themeHelper->getLinkTopMessage();
        $variables['top_message'] = $themeHelper->getTopMessage();
        $variables['link_instagram'] = $themeHelper->getUrlSocialInstagram();
        $variables['link_facebook'] = $themeHelper->getUrlSocialFacebook();
        $variables['link_twitter'] = $themeHelper->getUrlSocialTwitter();
        $variables['link_pin'] = $themeHelper->getUrlSocialPin();

        return parent::addEmailVariables($variables, $storeId);
    }
}

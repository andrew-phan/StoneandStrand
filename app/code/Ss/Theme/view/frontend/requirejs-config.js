
/**
 * Magestore
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
 * @package     Magestore_Megamenu
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
var config = {
    map: {
        '*': {
            'Cookies': 'Ss_Theme/js/lib/js.cookie',
            'ss_script': 'Ss_Theme/js/ss_script'
        }
    },
    deps: ['jquery'],
    shim:{
        'Ss_Theme/js/lib/modernizr.2.8.3' : ['jquery'],
        'Ss_Theme/js/lib/handlebars-v4.0.5' : ['jquery'],
        'Ss_Theme/js/lib/detectizr' : ['jquery'],
        'Ss_Theme/js/lib/jquery.lazy' : ['jquery'],
        'Ss_Theme/js/lib/parsley' : ['jquery'],
        // 'Ss_Theme/js/lib/js.cookie' : ['jquery'],
        'Ss_Theme/js/lib/slick' : ['jquery']
    }
    // ,
    // "paths": {
    //   'libs':'Ss_Theme/js/libs'
    // }
};
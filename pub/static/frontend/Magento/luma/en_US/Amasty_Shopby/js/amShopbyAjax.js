/**
 * @author    Amasty Team
 * @copyright Copyright (c) Amasty Ltd. ( http://www.amasty.com/ )
 * @package   Amasty_Shopby
 */
define([
    "jquery",
    "jquery/ui",
    "Amasty_Shopby/js/amShopby",
    "productListToolbarForm"
], function ($) {
    'use strict';
    $.widget('mage.amShopbyAjax',{
        options:{
            _isAmshopbyAjaxProcessed: false
        },
        _create: function (){
            var self = this;
            $(function(){
                self.initAjax();
            });

        },

        updateContent: function(link){
            var self = this;
            $("#amasty-shopby-overlay").show();
            if (typeof window.history.pushState === 'function') {
                window.history.pushState({url: link}, '', link);
            }
            $.getJSON(link, {isAjax: 1}, function(data){
                $('#layered-filter-block').html(data.navigation);
                $('#layered-filter-block').trigger('contentUpdated');
                $('#amasty-shopby-product-list').html(data.categoryProducts);
                $('#amasty-shopby-product-list').trigger('contentUpdated');
                $("#amasty-shopby-overlay").hide();
                self.initAjax();
                $(window).trigger('updateContentFilter');
                $('#amasty-shopby-product-list').trigger('afterAjaxPaging');
            });
        },

        initAjax: function()
        {
            var self = this;
            $.mage.amShopbyFilterAbstract.prototype.apply = function(link){
                self.updateContent(link);
            }
            this.options._isAmshopbyAjaxProcessed = false;
            $.mage.productListToolbarForm.prototype.changeUrl = function (paramName, paramValue, defaultValue) {
                if(self.options._isAmshopbyAjaxProcessed) {
                    return;
                }
                self.options._isAmshopbyAjaxProcessed = true;
                var urlPaths = this.options.url.split('?'),
                    baseUrl = urlPaths[0],
                    urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                    paramData = {},
                    parameters;
                for (var i = 0; i < urlParams.length; i++) {
                    parameters = urlParams[i].split('=');
                    paramData[parameters[0]] = parameters[1] !== undefined
                        ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                        : '';
                }
                paramData[paramName] = paramValue;
                if (paramValue == defaultValue) {
                    delete paramData[paramName];
                }
                paramData = $.param(paramData);

                //location.href = baseUrl + (paramData.length ? '?' + paramData : '');
                self.updateContent(baseUrl + (paramData.length ? '?' + paramData : ''));
            }
            var changeFunction = function(e){
                self.updateContent($(this).prop('href'));
                e.stopPropagation();
                e.preventDefault();
            };
            $(".swatch-option-link-layered").bind('click', changeFunction);
            $(".filter-current a").bind('click',changeFunction);
            $(".filter-actions a").bind('click', changeFunction);
            $(".toolbar .pages a").bind('click', changeFunction);
        }
    });
    
});

define([
    'uiComponent',
    'ko'
], function (Component, ko) {
    'use strict';

    var ss_reason_buy = window.checkoutConfig.ss_reason_buy;

    return Component.extend({
        defaults: {
            template: 'Ss_Checkout/form/element/reason-buy'
        },
        ss_reason_buy: ko.observableArray(ss_reason_buy),
    });
});
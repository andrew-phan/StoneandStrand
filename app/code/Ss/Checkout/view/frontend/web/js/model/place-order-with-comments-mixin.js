/**
 * Copyright © 2015 Customer Paradigm. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (placeOrderAction) {
        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, redirectOnSuccess, messageContainer) {
            // adding order comments
            var order_comments = jQuery('#ss-product-information').val();
            if (paymentData.additional_data === null) {
                paymentData.additional_data = {comments: order_comments};
            } else {
                paymentData.additional_data.comments = order_comments;
            }

            return originalAction(paymentData, redirectOnSuccess, messageContainer);
        });
    };
});
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define([
            'jquery',
            'Magento_Payment/js/model/credit-card-validation/cvv-validator',
            'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator',
            'Magento_Payment/js/model/credit-card-validation/expiration-date-validator/expiration-year-validator',
            'Magento_Payment/js/model/credit-card-validation/expiration-date-validator/expiration-month-validator',
            'Magento_Payment/js/model/credit-card-validation/credit-card-data'
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($, cvvValidator, creditCardNumberValidator, expirationDateValidator, monthValidator, creditCardData) {
    "use strict";

    /**
     * Javascript object with credit card types
     * 0 - regexp for card number
     * 1 - regexp for cvn
     * 2 - check or not credit card number trough Luhn algorithm by
     */
    var creditCartTypes = {
        'SO': [new RegExp('^(6334[5-9]([0-9]{11}|[0-9]{13,14}))|(6767([0-9]{12}|[0-9]{14,15}))$'), new RegExp('^([0-9]{3}|[0-9]{4})?$'), true],
        'SM': [new RegExp('(^(5[0678])[0-9]{11,18}$)|(^(6[^05])[0-9]{11,18}$)|(^(601)[^1][0-9]{9,16}$)|(^(6011)[0-9]{9,11}$)|(^(6011)[0-9]{13,16}$)|(^(65)[0-9]{11,13}$)|(^(65)[0-9]{15,18}$)|(^(49030)[2-9]([0-9]{10}$|[0-9]{12,13}$))|(^(49033)[5-9]([0-9]{10}$|[0-9]{12,13}$))|(^(49110)[1-2]([0-9]{10}$|[0-9]{12,13}$))|(^(49117)[4-9]([0-9]{10}$|[0-9]{12,13}$))|(^(49118)[0-2]([0-9]{10}$|[0-9]{12,13}$))|(^(4936)([0-9]{12}$|[0-9]{14,15}$))'), new RegExp('^([0-9]{3}|[0-9]{4})?$'), true],
        'VI': [new RegExp('^4[0-9]{12}([0-9]{3})?$'), new RegExp('^[0-9]{3}$'), true],
        'MC': [new RegExp('^5[1-5][0-9]{14}$'), new RegExp('^[0-9]{3}$'), true],
        'AE': [new RegExp('^3[47][0-9]{13}$'), new RegExp('^[0-9]{4}$'), true],
        'DI': [new RegExp('^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}|5[0-9]{14}))$'), new RegExp('^[0-9]{3}$'), true],
        'JCB': [new RegExp('^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}|5[0-9]{14}))$'), new RegExp('^[0-9]{3}$'), true],
        'OT': [new RegExp('^([0-9]+)$'), new RegExp('^([0-9]{3}|[0-9]{4})?$'), false],
        'DN': [new RegExp('^3((0([0-5]\\d*)?)|[689]\\d*)?$'), new RegExp('^[0-9]{3}$'), true],
        'UN': [new RegExp('^6(2\\d*)?$'), new RegExp('^[0-9]{3}$'), true],
        'MI': [new RegExp('^((5((0|[6-9])\\d*)?)|(6|6[37]\\d*))$'), new RegExp('^[0-9]{3}$'), true]
    };

    /**
     * validate credit card number using mod10
     * @param s
     * @return {Boolean}
     */
    function validateCreditCard(s) {
        // remove non-numerics
        var v = "0123456789",
            w = "", i, j, k, m, c, a, x;
        for (i = 0; i < s.length; i++) {
            x = s.charAt(i);
            if (v.indexOf(x, 0) != -1)
                w += x;
        }
        // validate number
        j = w.length / 2;
        k = Math.floor(j);
        m = Math.ceil(j) - k;
        c = 0;
        for (i = 0; i < k; i++) {
            a = w.charAt(i * 2 + m) * 2;
            c += a > 9 ? Math.floor(a / 10 + a % 10) : a;
        }
        for (i = 0; i < k + m; i++) {
            c += w.charAt(i * 2 + 1 - m) * 1;
        }
        return (c % 10 === 0);
    }

    $.each({
        'required-number': [
            function (v) {
                return !!v.length;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Please enter a valid number in this field.</p>'
        ],
        'validate-card-type': [
            function (number, item, allowedTypes) {
                var cardInfo,
                    i,
                    l;

                if (!creditCardNumberValidator(number).isValid) {
                    return false;
                } else {
                    cardInfo = creditCardNumberValidator(number).card;

                    for (i = 0, l = allowedTypes.length; i < l; i++) {
                        if (cardInfo.title == allowedTypes[i].type) {
                            return true;
                        }
                    }
                    return false;
                }
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Please enter a valid credit card type number.</p>'
        ],
        'validate-card-number': [
            /**
             * Validate credit card number based on mod 10
             * @param number - credit card number
             * @return {boolean}
             */
                function (number) {
                return creditCardNumberValidator(number).isValid;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Please enter a valid credit card number.</p>'
        ],
        'validate-card-date': [
            /**
             * Validate credit card number based on mod 10
             * @param date - month
             * @return {boolean}
             */
                function (date) {
                return monthValidator(date).isValid;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Incorrect credit card expiration month.</p>'
        ],
        'validate-card-cvv': [
            /**
             * Validate credit card number based on mod 10
             * @param cvv - month
             * @return {boolean}
             */
                function (cvv) {
                var maxLength = creditCardData.creditCard ? creditCardData.creditCard.code.size : 3;
                return cvvValidator(cvv, maxLength).isValid;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Please enter a valid credit card verification number.</p>'
        ],
        'validate-card-year': [
            /**
             * Validate credit card number based on mod 10
             * @param date - month
             * @return {boolean}
             */
                function (date) {
                return monthValidator(date).isValid;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Incorrect credit card expiration year.</p>'
        ],
        'required': [
            function (v) {
                return !!v.length;
            },
            '<span class="img-icon icon-error" data-toggle-error>&nbsp;</span><p class="hidden">This is a required field.</p>'
        ],
        'validate-cc-exp': [
            /**
             * Validate credit card expiration date, make sure it's within the year and not before current month
             * @param value - month
             * @param element - element contains month
             * @param params - year selector
             * @return {Boolean}
             */
            function (value, element, params) {
                var isValid = false;
                if (value && params) {
                    var month = value,
                        year = $(params).val(),
                        currentTime = new Date(),
                        currentMonth = currentTime.getMonth() + 1,
                        currentYear = currentTime.getFullYear();
                    isValid = !year || year > currentYear || (year == currentYear && month >= currentMonth);
                }
                return isValid;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Incorrect credit card expiration date.</p>'
        ],
        "validate-cc-type-select": [
            /**
             * Validate credit card type matches credit card number
             * @param value - select credit card type
             * @param element - element contains the select box for credit card types
             * @param params - selector for credit card number
             * @return {boolean}
             */
                function (value, element, params) {
                if (value && params && creditCartTypes[value]) {
                    return creditCartTypes[value][0].test($(params).val().replace(/\s+/g, ''));
                }
                return false;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Card type does not match credit card number.</p>'
        ],
        "validate-cc-number": [
            /**
             * Validate credit card number based on mod 10
             * @param value - credit card number
             * @return {boolean}
             */
                function (value) {
                if (value) {
                    return validateCreditCard(value);
                }
                return false;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Please enter a valid credit card number.</p>'
        ],
        "validate-cc-type": [
            /**
             * Validate credit card number is for the correct credit card type
             * @param value - credit card number
             * @param element - element contains credit card number
             * @param params - selector for credit card type
             * @return {boolean}
             */
                function (value, element, params) {
                if (value && params) {
                    var ccType = $(params).val();
                    value = value.replace(/\s/g, '').replace(/\-/g, '');
                    if (creditCartTypes[ccType] && creditCartTypes[ccType][0]) {
                        return creditCartTypes[ccType][0].test(value);
                    } else if (creditCartTypes[ccType] && !creditCartTypes[ccType][0]) {
                        return true;
                    }
                }
                return false;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Credit card number does not match credit card type.</p>'
        ],
        "validate-cc-cvn": [
            /**
             * Validate credit card cvn based on credit card type
             * @param value - credit card cvn
             * @param element - element contains credit card cvn
             * @param params - credit card type selector
             * @return {*}
             */
                function (value, element, params) {
                if (value && params) {
                    var ccType = $(params).val();
                    if (creditCartTypes[ccType] && creditCartTypes[ccType][0]) {
                        return creditCartTypes[ccType][1].test(value);
                    }
                }
                return false;
            },
            '<span class="img-icon icon-error" data-toggle-error="">&nbsp;</span><p class="hidden">Please enter a valid credit card verification number.</p>'
        ],

    }, function (i, rule) {
        rule.unshift(i);
        $.validator.addMethod.apply($.validator, rule);
    });
}));
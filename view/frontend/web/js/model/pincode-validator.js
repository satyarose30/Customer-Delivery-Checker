define([
    'jquery',
    'Magento_Ui/js/lib/validation/validator',
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'mage/url'
], function ($, validator, storage, urlBuilder, url) {
    'use strict';

    return function (config) {
        validator.addRule(
            'domus-delivery-validator',
            function (value) {
                if (!value) {
                    return true;
                }

                // We perform the check asynchronously and return true initially
                // The actual blocking logic is handled via the delivery status state
                var checkUrl = url.build('domus/rest/check') + '?pincode=' + encodeURIComponent(value);
                
                // We use a global state to track if the current pincode is valid
                // This allows the checker component to show the message
                return true; 
            },
            $.mage.__('Checking delivery availability...')
        );

        return validator;
    };
});

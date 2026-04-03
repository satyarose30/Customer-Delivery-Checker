define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/storage'
], function ($, ko, Component, quote, storage) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Domus_CustomerDeliveryChecker/checkout/pincode-status',
            lastCheckedPincode: ''
        },

        initialize: function () {
            this._super();
            this.isLoading = ko.observable(false);
            this.checkResult = ko.observable(null);
            this.initSubscriptions();
            this.checkCurrentAddress();
            return this;
        },

        initSubscriptions: function () {
            var self = this;

            quote.shippingAddress.subscribe(function (newAddress) {
                if (newAddress && newAddress.postcode) {
                    if (self.lastCheckedPincode !== newAddress.postcode) {
                        self.checkAddress(newAddress.postcode, newAddress.countryId);
                    }
                }
            });

            quote.shippingMethod.subscribe(function () {
                var shippingAddress = quote.shippingAddress();
                if (shippingAddress && shippingAddress.postcode
                    && self.lastCheckedPincode !== shippingAddress.postcode
                ) {
                    self.checkAddress(shippingAddress.postcode, shippingAddress.countryId);
                }
            });
        },

        checkCurrentAddress: function () {
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress && shippingAddress.postcode) {
                this.checkAddress(shippingAddress.postcode, shippingAddress.countryId);
            }
        },

        checkAddress: function (pincode, countryId) {
            var self = this;

            if (!pincode) {
                return;
            }

            this.lastCheckedPincode = pincode;
            this.isLoading(true);

            var url = window.BASE_URL
                ? window.BASE_URL + 'domus/rest/check'
                : '/domus/rest/check';
            url += '?pincode=' + encodeURIComponent(pincode) +
                   '&country_id=' + encodeURIComponent(countryId || 'IN');

            storage.get(url, undefined, true)
                .done(function (response) {
                    self.checkResult(response);
                    self.isLoading(false);
                })
                .fail(function () {
                    self.checkResult(null);
                    self.isLoading(false);
                });
        },

        isDeliverable: function () {
            return this.checkResult() && this.checkResult().is_available;
        },

        isNotDeliverable: function () {
            return this.checkResult() && !this.checkResult().is_available;
        },

        isResultAvailable: function () {
            return this.checkResult() !== null;
        },

        getMessage: function () {
            return this.checkResult() ? this.checkResult().message : '';
        },

        isCodAvailable: function () {
            return this.checkResult() && this.checkResult().is_cod_available;
        }
    });
});

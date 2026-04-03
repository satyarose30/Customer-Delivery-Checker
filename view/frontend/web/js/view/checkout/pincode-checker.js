define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'mage/storage'
], function ($, ko, Component, quote, customer, storage) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Domus_CustomerDeliveryChecker/checkout/pincode-checker',
            checkUrl: '',
            defaultCountry: 'IN',
            pincodeLabel: 'Enter your Pincode',
            checkButtonText: 'Check',
            changeButtonText: 'Change'
        },

        initialize: function () {
            this._super();
            this.pincode = ko.observable('');
            this.isLoading = ko.observable(false);
            this.isChecked = ko.observable(false);
            this.checkResult = ko.observable(null);
            this.errorMessage = ko.observable('');
            this.initFromShippingAddress();
            return this;
        },

        initFromShippingAddress: function () {
            var self = this;

            // Initial check if postcode already exists (e.g., logged-in customer with saved address)
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress && shippingAddress.postcode) {
                this.pincode(shippingAddress.postcode);
                this.checkPincodeSilent(shippingAddress.postcode);
            }

            // Sync with standard checkout postcode field
            quote.shippingAddress.subscribe(function (newAddress) {
                if (newAddress && newAddress.postcode) {
                    if (self.pincode() !== newAddress.postcode) {
                        self.pincode(newAddress.postcode);
                        self.checkPincodeSilent(newAddress.postcode);
                    }
                } else if (!newAddress || !newAddress.postcode) {
                    self.isChecked(false);
                    self.checkResult(null);
                    self.pincode('');
                }
            });
        },

        checkPincodeSilent: function (pincode) {
            var self = this,
                trimmedPincode = (pincode || '').trim();

            if (!trimmedPincode || trimmedPincode.length < 5) {
                this.isChecked(false);
                this.checkResult(null);
                return;
            }

            this.isLoading(true);
            this.errorMessage('');

            this.performCheck(trimmedPincode)
                .done(function (response) {
                    self.checkResult(response);
                    self.isChecked(true);
                    self.isLoading(false);
                    
                    // If not deliverable, we can trigger a validation error on the field if needed
                    // For now, we show the message in our template
                })
                .fail(function () {
                    self.errorMessage('Unable to verify delivery for this pincode.');
                    self.isLoading(false);
                    self.isChecked(false);
                });
        },

        performCheck: function (pincode) {
            var url = this.checkUrl + '?pincode=' + encodeURIComponent(pincode) +
                      '&countryId=' + encodeURIComponent(this.defaultCountry);
            return storage.get(url, undefined, true);
        },

        isDeliverable: function () {
            return this.checkResult() && this.checkResult().is_available;
        },

        isNotDeliverable: function () {
            return this.checkResult() && !this.checkResult().is_available;
        },

        isCodAvailable: function () {
            return this.checkResult() && this.checkResult().is_cod_available;
        },

        getMessage: function () {
            return this.checkResult() ? this.checkResult().message : '';
        },

        getDeliveryInfo: function () {
            if (!this.checkResult()) {
                return '';
            }
            var parts = [],
                result = this.checkResult();
            if (result.city) {
                parts.push(result.city);
            }
            if (result.state) {
                parts.push(result.state);
            }
            if (result.area_name) {
                parts.push(result.area_name);
            }
            return parts.join(', ');
        }
    });
});

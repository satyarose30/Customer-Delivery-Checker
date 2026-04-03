define([
    'jquery',
    'mage/url',
    'mage/validation',
    'Magento_Ui/js/modal/alert',
    'domReady!'
], function ($, urlBuilder, validation, alert) {
    'use strict';

    $.widget('domus.pincodeChecker', {
        options: {
            checkUrl: '',
            warrantyUrl: '',
            productId: null,
            apiKey: '',
            pincodes: [],
            showWarranty: false
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._bindEvents();
            
            // Load warranty info if enabled and product ID exists
            if (this.options.showWarranty && this.options.productId) {
                this.loadWarrantyInfo();
            }
        },

        /**
         * Bind events
         */
        _bindEvents: function () {
            var self = this;
            
            // Pincode check button click
            $('#pincode-check-btn').on('click', function () {
                self.checkPincode();
            });
            
            // Enter key on pincode input
            $('#pincode-input').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    self.checkPincode();
                }
            });
            
            // Pincode input validation
            $('#pincode-input').on('input', function () {
                var value = $(this).val();
                // Only allow numbers and limit to 6 digits
                $(this).val(value.replace(/[^0-9]/g, '').slice(0, 6));
            });
        },

        /**
         * Check pincode availability
         */
        checkPincode: function () {
            var self = this,
                pincode = $('#pincode-input').val(),
                $result = $('#pincode-result'),
                $loader = $('#pincode-loader');

            if (!pincode || pincode.length !== 6) {
                alert({
                    title: 'Error',
                    content: 'Please enter a valid 6-digit pincode'
                });
                return;
            }

            // Show loader, hide previous results
            $loader.show();
            $result.hide();

            // Prepare request data
            var requestData = {
                pincode: pincode,
                product_id: this.options.productId
            };

            // Make AJAX request
            $.ajax({
                url: this.options.checkUrl,
                type: 'GET',
                data: requestData,
                dataType: 'json',
                success: function (response) {
                    self.handlePincodeResponse(response);
                },
                error: function () {
                    self.handleError();
                },
                complete: function () {
                    $loader.hide();
                }
            });
        },

        /**
         * Handle pincode check response
         */
        handlePincodeResponse: function (response) {
            var $result = $('#pincode-result'),
                $message = $result.find('.message'),
                $details = $result.find('.details'),
                html = '';

            if (response.success) {
                // Success message
                $message.removeClass('error').addClass('success')
                    .html(response.message || 'Delivery available');

                // Additional details
                if (response.estimated_days) {
                    html += '<div class="estimated-days">' +
                        'Estimated Delivery: ' + response.estimated_days + ' days' +
                        '</div>';
                }
                
                if (response.cash_on_delivery) {
                    html += '<div class="cod-available">' +
                        '✓ Cash on Delivery Available' +
                        '</div>';
                }

                // Show warranty if available
                if (response.warranty && response.has_warranty) {
                    html += this.formatWarranty(response.warranty);
                }

                $details.html(html);
            } else {
                // Error message
                $message.removeClass('success').addClass('error')
                    .html(response.message || 'Delivery not available');
                $details.html('');
            }

            $result.show();
        },

        /**
         * Load warranty information
         */
        loadWarrantyInfo: function () {
            var self = this;

            if (!this.options.productId) {
                return;
            }

            $.ajax({
                url: this.options.warrantyUrl,
                type: 'GET',
                data: { product_id: this.options.productId },
                dataType: 'json',
                success: function (response) {
                    self.handleWarrantyResponse(response);
                },
                error: function () {
                    console.warn('Failed to load warranty information');
                }
            });
        },

        /**
         * Handle warranty response
         */
        handleWarrantyResponse: function (response) {
            var $warrantyInfo = $('#warranty-info'),
                $warrantyContent = $warrantyInfo.find('.warranty-content'),
                html = '';

            if (response.success && response.has_warranty) {
                var warranty = response.warranty;

                if (warranty.warranty_type) {
                    html += '<div class="warranty-type">' +
                        '<strong>Warranty Type:</strong> ' + warranty.warranty_type +
                        '</div>';
                }

                if (warranty.warranty_duration) {
                    html += '<div class="warranty-duration">' +
                        '<strong>Duration:</strong> ' + warranty.warranty_duration +
                        '</div>';
                }

                if (warranty.warranty_details) {
                    html += '<div class="warranty-details">' +
                        '<strong>Details:</strong> ' + warranty.warranty_details.replace(/\n/g, '<br>') +
                        '</div>';
                }

                if (html) {
                    $warrantyContent.html(html);
                    $warrantyInfo.show();
                }
            }
        },

        /**
         * Format warranty HTML
         */
        formatWarranty: function (warranty) {
            var html = '<div class="warranty-summary">' +
                '<h4>Warranty Information</h4>';

            if (warranty.warranty_type) {
                html += '<div><strong>Type:</strong> ' + warranty.warranty_type + '</div>';
            }

            if (warranty.warranty_duration) {
                html += '<div><strong>Duration:</strong> ' + warranty.warranty_duration + '</div>';
            }

            if (warranty.warranty_details) {
                html += '<div><strong>Details:</strong> ' + 
                    warranty.warranty_details.substring(0, 100) + 
                    (warranty.warranty_details.length > 100 ? '...' : '') + '</div>';
            }

            html += '</div>';
            return html;
        },

        /**
         * Handle error
         */
        handleError: function () {
            var $result = $('#pincode-result'),
                $message = $result.find('.message');

            $message.removeClass('success').addClass('error')
                .html('An error occurred. Please try again.');
            $result.find('.details').html('');
            $result.show();
        }
    });

    return $.domus.pincodeChecker;
});

define([
    'jquery',
    'mage/url',
    'domReady!',
    'mage/calendar'
], function ($, urlBuilder) {
    'use strict';

    $.widget('domus.timeSlotSelector', {
        options: {
            slotsUrl: '/rest/V1/customerdeliverychecker/rest/timeSlots',
            pincodeInput: '#pincode-input',
            slotContainer: '#time-slots-container',
            dateInput: '#delivery-date',
            slotInput: '#delivery-slot'
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._bindEvents();
            this._initializeCalendar();
        },

        /**
         * Bind events
         */
        _bindEvents: function () {
            var self = this;
            
            $(this.options.pincodeInput).on('change', function () {
                self.loadTimeSlots();
            });

            $(this.options.dateInput).on('change', function () {
                self.loadTimeSlots();
            });
        },

        /**
         * Initialize calendar
         */
        _initializeCalendar: function () {
            $(this.options.dateInput).calendar({
                showTime: false,
                minDate: new Date(),
                onSelect: function () {
                    $(this).trigger('change');
                }
            });
        },

        /**
         * Load available time slots
         */
        loadTimeSlots: function () {
            var self = this,
                pincode = $(this.options.pincodeInput).val(),
                date = $(this.options.dateInput).val();

            if (!pincode || pincode.length !== 6) {
                $(this.options.slotContainer).hide();
                return;
            }

            $(this.options.slotContainer).addClass('loading');

            $.ajax({
                url: this.options.slotsUrl,
                type: 'GET',
                data: {
                    pincode: pincode,
                    date: date
                },
                dataType: 'json',
                success: function (response) {
                    self.renderTimeSlots(response);
                },
                error: function () {
                    self.showError();
                },
                complete: function () {
                    $(self.options.slotContainer).removeClass('loading');
                }
            });
        },

        /**
         * Render time slots
         */
        renderTimeSlots: function (response) {
            var self = this,
                $container = $(this.options.slotContainer),
                html = '';

            if (!response.success) {
                html = '<div class="message error">' + response.message + '</div>';
                $container.html(html).show();
                return;
            }

            html = '<div class="time-slots-header">' +
                '<h4>Available Delivery Slots for ' + response.date + '</h4>' +
                '</div>' +
                '<div class="time-slots-grid">';

            $.each(response.available_slots, function (index, slot) {
                html += '<div class="time-slot" data-slot="' + slot.value + '">' +
                    '<div class="slot-label">' + slot.label + '</div>' +
                    '<div class="slot-time-range">' + slot.time_range[0] + ' - ' + slot.time_range[1] + '</div>' +
                    '<div class="slot-eta">Est. Delivery: ' + slot.estimated_time + '</div>' +
                    '</div>';
            });

            html += '</div>';

            $container.html(html).show();
            this._bindSlotSelection();
        },

        /**
         * Bind slot selection events
         */
        _bindSlotSelection: function () {
            var self = this;
            
            $(this.options.slotContainer).find('.time-slot').on('click', function () {
                $(this).siblings().removeClass('selected');
                $(this).addClass('selected');
                
                var slotValue = $(this).data('slot');
                $(self.options.slotInput).val(slotValue);
                
                // Trigger custom event
                $(self.options.slotInput).trigger('slot:selected', [slotValue]);
            });
        },

        /**
         * Show error message
         */
        showError: function () {
            var $container = $(this.options.slotContainer);
            $container.html('<div class="message error">Unable to load time slots</div>').show();
        }
    });

    return $.domus.timeSlotSelector;
});

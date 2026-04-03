define([
    'jquery',
    'ko',
    'uiComponent'
], function ($, ko, Component) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Domus_CustomerDeliveryChecker/delivery-tracker'
        },
        initialize: function () {
            this._super();
            // Tracking initialization logic
        }
    });
});

/**
 * @api
 * Pincode map visualization component
 */
define([
    'jquery',
    'uiComponent',
    'ko'
], function ($, Component, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Domus_CustomerDeliveryChecker/map/pincode-map',
            mapApiKey: '',
            mapOptions: {
                zoom: 5,
                center: {lat: 20.5937, lng: 78.9629} // Default India coordinates
            }
        },

        initialize: function () {
            this._super();
            this.map = null;
            this.markers = [];
            
            // Map initialization binding
            ko.bindingHandlers.googleMap = {
                init: function (element, valueAccessor, allBindings, viewModel) {
                    viewModel.initMap(element);
                }
            };
            
            return this;
        },

        initMap: function (element) {
            if (typeof google === 'undefined' || !google.maps) {
                console.warn('Google Maps API is not loaded.');
                return;
            }
            
            this.map = new google.maps.Map(element, this.mapOptions);
        },
        
        addPincodeMarker: function (lat, lng, title) {
            if (!this.map || !lat || !lng) return;
            
            var marker = new google.maps.Marker({
                position: {lat: parseFloat(lat), lng: parseFloat(lng)},
                map: this.map,
                title: title
            });
            this.markers.push(marker);
        },
        
        clearMarkers: function () {
            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(null);
            }
            this.markers = [];
        }
    });
});

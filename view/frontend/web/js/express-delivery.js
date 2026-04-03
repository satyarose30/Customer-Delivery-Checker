define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';
    return function (config, element) {
        // Express delivery logic
        $(element).on('click', '#upgrade-express-btn', function() {
            console.log('Express upgrade triggered.');
        });
    };
});

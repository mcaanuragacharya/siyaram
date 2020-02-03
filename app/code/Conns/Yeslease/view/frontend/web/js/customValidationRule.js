define([
    'jquery'
	'jquery/ui',
	'jquery/validate',
	'mage/translate'
],  function ($, ko, Component) {
       "use strict";
       return Component.extend({
           defaults: {
  template: 'app\code\Conns\Yeslease\view\frontend\web\template\creditcard.html'
           },

    return function () {
        $.validator.addMethod(
            "validate-ssn-last",
            function(value, element) {
                var length = 4;
                return (value.length == length && !isNaN(value));
            },
            $.mage.__("Please enter valid last 4 digits of your SSN")
        );
        $.validator.addMethod(
            "required-insurance",
            function(value,element) {
                return $(element).prop('checked');
            },
            $.mage.__("Please select the checkbox above.")
        );
    }
});

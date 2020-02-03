/**
 * Copyright Â© Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote',
    'mage/url',
	'mage/validation',
	"mage/calendar"
], function ($,ko,Component,quote,url) {
    'use strict';
	
	

    return Component.extend({

        defaults: {
            template: 'Conns_Yeslease/payment/progressive',
			image : window.checkoutConfig.payment.progressive.image,
			autoLeaseNumberForCheckout : window.checkoutConfig.payment.progressive.autoLeaseNumberForCheckout,
            openEyeImage : window.checkoutConfig.payment.progressive.openEyeImage,
            closeEyeImage : window.checkoutConfig.payment.progressive.closeEyeImage,
        },
		
		validateForm: function (form) {
                 return $(form).validation() && $(form).validation('isValid');
        },
		
			
        verify : function () {
            var self = this;
			var leasenumber		=	$("#applicant_lease_number").val();
			var last4ssn		=	$("#applicant_lastfour_ssn").val();
			var applicant_dob	=	$("#applicant_dob").val();
			var apiUrl			=	'';
			if(applicant_dob && (applicant_dob != '') && (applicant_dob != 'undefined')){
				apiUrl	=	'/yeslease/checkout/searchlease';
			
			}else if((leasenumber) && (leasenumber != '') && (leasenumber != 'undefined')) 
			{
				apiUrl	=	'/yeslease/checkout/verifylease';
			}
			if((!last4ssn) && (last4ssn == '') && (last4ssn != 'undefined')){
				alert('blank last 4 ssn');
			}
			
			if (!this.validateForm('#conns-progressive-form')) {
			   return;
			}
			
			$.ajax({
				url : apiUrl,
				method: "POST",
				showLoader: true,
				dataType: 'json',
				data : $('#conns-progressive-form').serialize(),
				success: function(result, textStatus, jqXHR) {
					//var result = JSON.parse(result);
					showLoader: false,
					alert(result);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert("error occured in request processing");
					//window.location.href = '.';
				}
            });
            
        },
		eyeclick : function () {
            $("#applicant_lastfour_ssn").attr("type") == "password" ?
                $("#applicant_lastfour_ssn").attr("type","text") :
                $("#applicant_lastfour_ssn").attr("type","password");
            $("#applicant_lastfour_ssn").attr("type") == "password" ?
                $("#yeslease-eye-icon").attr("src",this.closeEyeImage) :
                $("#yeslease-eye-icon").attr("src",this.openEyeImage);
        },
		isNumber : function (data,event) {
            var iKeyCode = (event.which) ? event.which : event.keyCode;
            if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57)) {
                return false;
            }
            return true;
        },
		fieldInit : function () {
            $("#conns-progressive-continue").prop("disabled", true);
			
        },
		
		showcalender: function () {
            $("#applicant_dob").calendar({
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				yearRange: "1901:2002",
                buttonText: "Select Date"

			});
			
        },
		clickhere : function () {
            $("#applicant_dob").calendar({
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				yearRange: "1901:2002",
                buttonText: "Select Date"
			});

			var currentApiName	=	$('#yl-click-here').val();
			if(currentApiName=='verifylease'){
			
				$('.onepage-leaseid').css('display','none');
				$('#applicant_lease_number').val('');
				$('.onepage-lease-dob').css('display','block');
				$('#yl-click-here').attr('value','searchlease');
				$('#forgetleaseid-content').html('Have approved Lease ID');
			}else{
				$('#applicant_dob').val('');
				$('.onepage-leaseid').css('display','block');
				$('.onepage-lease-dob').css('display','none');
				$('#yl-click-here').attr('value','verifylease');
				$('#forgetleaseid-content').html('Forgot Lease ID');
			}
        },
		progressiveApproved : function () {
            var self = this;
            var selection = $("#p_method_progressive").val();
            
			var paymentOption = $('#p_method_progressive').parents("div.payment-method._active");
			$(paymentOption).removeClass('selected-payment');
			$('.opc-order-review-block__content').show();
			$('html,body').animate({
				scrollTop: $(".opc-order-review-block").offset().top},
				'slow');
           
        },
    });
});
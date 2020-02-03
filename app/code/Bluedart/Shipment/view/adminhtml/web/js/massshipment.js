/*jQuery.noConflict();*/

/*require([
'jquery', // jquery Library
'mage/mage',
'jquery/ui', // Jquery UI Library
'jquery/validate', // Jquery Validation Library
'mage/translate' // Magento text translate (Validation message translte as per language)
], function($){ 
    $.validator.addMethod(
    'validate-custom-name', function (value) { 
    return (value !== 'test'); // Validation logic here
    }, $.mage.__('Enter Valid name'));
    
    var dataForm = $('#massshipmentform');
    dataForm.mage('validation', {});
});*/

function massBluedartShipment() {
    var selected = [];
    jQuery(".bluedart_result").empty().css('display','none');
    jQuery('.data-row input:checked').each(function() {
        selected.push(jQuery(this).parent().parent().next().children().text().trim());
    });
    if (selected.length === 0) {
        alert("Please Select orders.");
    }else{
        jQuery(".order_in_backgrounds").fadeIn(500);
        jQuery(".bluedart_mass").fadeIn(500);       
    }
}

function bluedartclose(){
    jQuery(".order_in_backgrounds").fadeOut(500);
    jQuery(".bluedart_mass").fadeOut(500);
}

function bluedartredirect(){
    window.location.reload(true);
}

function massShipmentsend(){
    var selected = [];
    var str = jQuery( "#massshipmentform" ).serialize();
    jQuery('.data-row input:checked').each(function() {
        selected.push(jQuery(this).parent().parent().next().children().text().trim());
    });
    bluedartclose();
    jQuery('.popup-loading').css('display','block');
    var url = jQuery('.bluedart_url').text();
    jQuery.ajax({
        url: url,
        type: "POST",
        data: {selectedOrders: selected, str:str, bulk:"bulk", form_key:FORM_KEY},
        success: function ajaxViewsSection(data) {
            jQuery('.popup-loading').css('display','none');
            jQuery(".bluedart_result").empty().css('display','none');
            jQuery(".order_in_backgrounds").fadeIn(500);
            jQuery(".bluedart_mass").fadeIn(500);
            jQuery(".bluedart_result").css("display","block");
            jQuery(".bluedart_result").append(data['Response']);
            jQuery(".bluedartclose" ).click(function() {
                bluedartredirect();
            });
        }
    });    
}

function ValidateAlpha(evt){
    var keyCode = (evt.which) ? evt.which : evt.keyCode
    if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32){
        return false;
    }
    return true;
}
function ValidateAlphaNumeric(evt){
    var keyCode = (evt.which) ? evt.which : evt.keyCode
    if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32 && keyCode > 31 && (keyCode < 48 || keyCode > 57))
        return false;
    return true;
}
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
} 
jQuery(document).ready(function(){
    /*jQuery.validator.addMethod("alphabets", function(value, element) {
        return this.optional(element) || /^[a-zA-Z]+$/i.test(value);
    }, "Please enter valid alphabets only.");
    jQuery.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^[A-Z0-9]+$/i.test(value);
    }, "Please enter valid data only.");*/

    jQuery("#massshipmentform").validate({
        messages: {
            service_productcode: "Please enter valid alphabets only.",
            service_packtype: "Please enter valid alphanumeric only.",
            service_pickupreadytime: "Please enter valid numbers only."
        },
        submitHandler: function (form) {
            massShipmentsend();
            return false;
        }
    });
    /*jQuery('#bluedart_shipment_creation_submit_id').on('click', function() {
        if(jQuery("#massshipmentform").valid()){
            massShipmentsend();
        }
    });*/
});
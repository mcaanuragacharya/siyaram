define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'BDPay_Billdesk/payment/billdesk'
            },
            getTitle:function(){

                return window.checkoutConfig.payment.billdesk.title;
            },
			afterPlaceOrder:function() {
				
			}
        });
    }
);
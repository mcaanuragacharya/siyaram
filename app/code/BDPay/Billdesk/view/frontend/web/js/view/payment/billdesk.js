define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'billdesk',
                component: 'BDPay_Billdesk/js/view/payment/method-renderer/billdesk-method'
            }
        );
        return Component.extend({});
    }
);
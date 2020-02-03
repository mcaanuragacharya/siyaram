/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'progressive',
            component: 'Conns_Yeslease/js/view/payment/method-renderer/progressive-method'
        }
    );


    return Component.extend({});
});

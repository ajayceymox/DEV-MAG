/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Zazmic_AmazonMCF/js/model/shipping-rates-validator',
        'Zazmic_AmazonMCF/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        amazonShippingRatesValidator,
        amazonShippingRatesValidationRules
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('zazmic', amazonShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('zazmic', amazonShippingRatesValidationRules);
        return Component;
    }
);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
define(
    [],
    function () {
        'use strict';
        return {
            getRules: function() {
                return {
                    'postcode': {
                        'required': true
                    }
                };
            }
        };
    }
)

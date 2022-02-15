/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
var config = {
    map: {
        '*':{
            'add-store':'Zazmic_AmazonMCF/js/add-store',
            'settings':'Zazmic_AmazonMCF/js/settings'
        }
    },
    shim:{
        'add-store':{
             deps: ['jquery']
        },
        'settings':{
            deps: ['jquery']
       },
    }
};


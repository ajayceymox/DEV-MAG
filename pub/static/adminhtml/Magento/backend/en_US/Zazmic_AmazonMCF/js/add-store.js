/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function(
        $,
        modal
    ) {
        var options = {
            type: 'popup',
            title: $.mage.__('New Amazon Store'),
            responsive: true,
            innerScroll: true,
            buttons: [{
                text: $.mage.__('Close'),
                class: '',
                click: function () {
                    this.closeModal();
                }
            }]
        };

        var popup = modal(options, $('#popup-mpdal'));
        $("#addStore").on('click',function(){
            $("#addstorePop").show();
            $("#popup-mpdal").modal("openModal");
        });

    }
)


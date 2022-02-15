/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
define(['jquery'], function($){
    "use strict";
        return function settings()
        {
            $('.tab').click(function() {
                var target =  $(this).attr("data-content-id");
                if($(this).hasClass('_show')){
                    $(this).removeClass('_show');
                    $('#'+target).removeClass('_show');
                    $('#'+target).addClass('_hide');
                }else{
                    $('#'+target).removeClass('_hide');
                    $(this).addClass('_show');
                    $('#'+target).addClass('_show');
                }
            });
            $(".sync_status").change(function() {
                var index =  $(this).attr("data-index");
                if(this.checked) {
                     
                    $('#interval-select-'+index).show();
                    $('.interval-select-'+index).show();
                    $('.interval-select-'+index).find('input').addClass('required-entry');
                    $('.show-'+index).show();
                }else{
                    $('#interval-select-'+index).hide();
                    $('.interval-select-'+index).hide();
                    $('.interval-select-'+index).find('input').removeClass('required-entry');
                    $('.interval-select-'+index).find('input').removeClass('mage-error');
                    $('.show-'+index).hide();
                }
            });
            $(".sync_status_backup").change(function() {
                var index =  $(this).attr("data-index");
                if(this.checked) {
                    $('.'+index).show();
                }else{                   
                    $('.'+index).hide();
                }
            });
            $('.tab_sub').click(function() {
                var target =  $(this).attr("data-content-id");
                console.log(target);
                if($(this).hasClass('_show')){
                    $(this).removeClass('_show');
                    $('#'+target).removeClass('_show');
                    $('#'+target).addClass('_hide');
                }else{
                    $('#'+target).removeClass('_hide');
                    $(this).addClass('_show');
                    $('#'+target).addClass('_show');
                }
            });

            $('.rate-text').on('input',function(e){
               var index = $(this).attr("data-index");
               var type = $(this).attr("data-index-type");
               var shippingRate = $("input[name='shipment_rates["+index+"]["+type+"_rate]']").val();
               var rateOver = $("input[name='shipment_rates["+index+"]["+type+"_order]']").val();
               var currency = $("input[name='currency["+index+"]").val();
               
               if(shippingRate == 0 && rateOver == 0){
                var text='Free shipping on all orders';
               }else{
                   if(rateOver > 0 ){
                    rateOver = 'over '+currency+rateOver; 
                   }    
                   if(shippingRate > 0){
                    shippingRate = 'Flat '+currency+shippingRate; 
                   }else{
                    shippingRate = 'Free';
                   }
                var text = shippingRate+' shipping on all orders '+rateOver;
               }
               $("textarea[name='shipment_rates["+index+"]["+type+"_desc]']").val(text);
            });
            $('#tab_contents_1').addClass("_show");
            $("div").find("[data-content-id='tab_contents_1']").addClass("_show");
        }
 });

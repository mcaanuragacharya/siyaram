require([
'jquery',
'configproduct'
], function ($, script) {
    if($('body').hasClass("page-product-configurable") && $('body').hasClass("catalog-product-view")){
        setTimeout(function(){ 
            if(!$('.swatch-opt .size .swatch-option:first').hasClass("selected")){  
                $('.swatch-opt .size .swatch-option:first').trigger("click");
                $('.swatch-opt .color .swatch-option:first').trigger("click");
            }
        }, 1000);
        setTimeout(function(){ 
            if(!$('.swatch-opt .size .swatch-option:first').hasClass("selected")){  
                $('.swatch-opt .size .swatch-option:first').trigger("click");
                $('.swatch-opt .color .swatch-option:first').trigger("click");
            }
        }, 2000);
        setTimeout(function(){ 
            if(!$('.swatch-opt .size .swatch-option:first').hasClass("selected")){  
                $('.swatch-opt .size .swatch-option:first').trigger("click");
                $('.swatch-opt .color .swatch-option:first').trigger("click");
            }
        }, 4000);
    }    
    setTimeout(function(){
    $('.filter-options-content .items .item').each(function() {
        if($(this).children('a').length){
            //alert($(this).html());            
        } else {
            $(this).hide();
        }
       // alert(cdata);
        //return false;
    });
    }, 2000);
    
});

require([
"jquery"
], function ($) {
    $(".click-me").click(function(){

        $("#popup-mpdal").addClass('open-popop');
    });

    $(".close-popop").click(function(){

        $("#popup-mpdal").removeClass('open-popop');
    });
});


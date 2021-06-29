define([
    'jquery',
], function ($) {
        $(document).ready(function (){
            $window = $(window);
                function alignBlockContent(selector) {
                    $(selector).css('height', '');
                    var minHeight = 0;
                    $(selector).each(function() {
                        if (minHeight < $(this).height()) {
                            minHeight = $(this).height();
                        }
                    });
                    if (minHeight > 0) {
                        $(selector).css('height', minHeight);
                    }
                }
                if ($(window).width() > 767)  {
                    setTimeout(function() {
                        // alignBlockContent('.education-item .education-item-name');
                        alignBlockContent('.education-item .education-item-description');
                    }, 500);
                    $(window).resize(function() {
                        setTimeout(function() {
                            // alignBlockContent('.education-item .education-item-details');
                            alignBlockContent('.education-item .education-item-details');
                        }, 500);
                    });
                }
        });
});

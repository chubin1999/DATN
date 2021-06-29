require([
    'jquery',
], function ($) {
    $(document).ready(function () {

        var maxHeightProDetail = 0;
        $(".product-item-info .product-item-details").each(function() {
            if ($(this).height() > maxHeightProDetail) {

                if ($(window).width() <= 1024) {
                    maxHeightProDetail = $(this).height();
                }else{
                    maxHeightProDetail = $(this).height() + 31;
                }
            }
        })
        $(".product-item-info .product-item-details").height(maxHeightProDetail);
        $(".catalog-category-view  .product-item-info .product-item-details, .catalogsearch-result-index  .product-item-info .product-item-details").height(maxHeightProDetail - 30);
    });
})

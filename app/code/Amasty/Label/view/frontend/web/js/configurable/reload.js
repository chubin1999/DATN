define([
    'jquery'
], function ($) {
    return function (imageContainer, productId, reloadUrl, inProductList) {
        if (!this.labels) {
            this.labels = [];
        }

        var imageBlock = imageContainer.find('.amasty-label-for-' + productId);

        if (this.labels.indexOf(productId) === -1 && imageBlock.length === 0) {
            this.labels.push(productId);
            $.ajax({
                url: reloadUrl,
                data: {
                    product_id: productId,
                    in_product_list: inProductList
                },
                method: 'GET',
                cache: true,
                dataType: 'json',
                showLoader: false
            }).done(function (data) {
                if (data.labels) {
                    imageContainer.last().after(data.labels).trigger('contentUpdated');
                }
            });
        }

        imageBlock.show();
    }
});

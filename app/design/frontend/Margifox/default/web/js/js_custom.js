require([
    'jquery',
    'Magento_Customer/js/customer-data',
    'slick',
    'domReady'
], function($, customerData) {
    var i = 0;

    function clickoff() {
        i++;
        if (jQuery('body.style-message .page.messages .mes-shopping').length) {
            setTimeout(
                function() {
                    jQuery('body.style-message .page.messages span.close').trigger('click');
                }, 5000);
        } else {
            if (i < 20) {
                setTimeout(clickoff, 500);
            }
        }
    }
    $(document).ready(function() {
        //add css for iframe
        if ($(".cms-become-a-stockist").length) {
            //add css for iframe
            var filecss = $("link[href*='becomeastockist']").attr('href');
            console.log(filecss);
            $('iframe').each(function(){
                function injectCSS(){
                    $iframe.contents().find('head').append(
                        $('<link/>', { rel: 'stylesheet', href: filecss, type: 'text/css' })
                    );
                }
                var $iframe = $(this);
                $iframe.on('load', injectCSS);
                injectCSS();
            });
            setTimeout(function(){
                //move iframe
                $(".hbspt-form").detach().appendTo('.become-a-stockist-form');
            }, 1000);

            $(".breadcrumbs").detach().appendTo('.breadcrumb-stockist');
        }

        jQuery('.button-contact .pagebuilder-button-secondary').attr("id", "click-to-chat__cta--text");
        jQuery('.button-contact .pagebuilder-button-secondary').removeAttr("href");
        jQuery('.button-contact .pagebuilder-button-secondary').attr('onclick', 'window.HubSpotConversations.widget.open();');


        clickoff();
        jQuery('button#product-addtocart-button').on('click', function() {
            setTimeout(
                function() {
                    clickoff();
                }, 5000);
        });
        // var customerInfo = customerData.get('customer')();

        // add class sticky
        var distance = $('.page-header').next().offset().top,
            $window = $(window);

        $window.scroll(function() {
            if ($window.scrollTop() >= distance) {
                $('header.page-header').addClass('sticky');
                $('.sidebar.sidebar-main').addClass('sticky-filter');
                if ($('.product-info-main').length) {
                    $('.product-info-main').addClass('sticky');
                }
            } else {
                $('header.page-header').removeClass('sticky');
                $('.sidebar.sidebar-main').removeClass('sticky-filter');
                if ($('.product-info-main').length) {
                    $('.product-info-main').removeClass('sticky');
                }
            }
        });
        // end add class sticky

        $window.scroll(function() {
            if ($('.checkout-cart-index .cart-container .cart-summary').length) {
                if ($window.scrollTop() >= 20) {
                    var info_height = $('.checkout-cart-index .cart-container .cart-summary').innerHeight() + 2;
                    var info_wrap_top = $('.checkout-cart-index .cart-container').offset().top;
                    var info_wrap_height = $('.checkout-cart-index .cart-container').innerHeight();
                    var info_wrap_bottom = info_wrap_top + info_wrap_height - 72;
                    var check_point = $window.scrollTop() + info_height + 173;
                    if (check_point >= info_wrap_bottom) {
                        $('.checkout-cart-index .cart-container .cart-summary').addClass('absoluted');
                    } else {
                        $('.checkout-cart-index .cart-container .cart-summary').removeClass('absoluted');
                    }
                } 
            }
        });


        //nav tablet
        $(document).on('click', 'header.page-header .nav-sections .ammenu-menu-wrapper.-desktop .ammenu-main-container .ammenu-items .ammenu-item', function(e) {
            if ($(window).width() >= 1024 && $(window).width() < 1200) {
              e.preventDefault();
              if($(this).hasClass("first-click")){
                window.location.href = $(this).find('.ammenu-link.-main.-parent').attr('href');
              } else {
                if($(this).children('.ammenu-submenu-container.ammenu-submenu.-full').length) {
                  $('header.page-header .nav-sections .ammenu-menu-wrapper.-desktop .ammenu-main-container .ammenu-items .ammenu-item').removeClass('first-click');
                  $(this).addClass('first-click');
                  if(!$('header.page-header').hasClass("first-click")){
                    $('header.page-header').addClass('first-click');
                  }
                } else {
                  window.location.href = $(this).find('.ammenu-link.-main').attr('href');
                }
              }
            }
        });
        $(document).on('click touch tap', function (e) {
            if ($(window).width() >= 1024 && $(window).width() < 1200) {
              var container = $('header.page-header .nav-sections');
              if (!container.is(e.target) && container.has(e.target).length === 0) {
                $('header.page-header .nav-sections .ammenu-menu-wrapper.-desktop .ammenu-main-container .ammenu-items .ammenu-item').removeClass('first-click');
                $('header.page-header').removeClass('first-click');
              }
            }
        });

        // move element after order view
        if ($('body.sales-order-view').length && $(window).width() < 767) {
            if ($('.order-details-items.ordered').length) {
                $target_ = $('.column.main').find('.nav.item.current');
                $('.order-details-items.ordered').insertAfter($target_);
                $('.order-details-items.ordered').css('display', 'block');
            }
        }

        if ($('body.sales-order-invoice').length && $(window).width() < 767) {
            if ($('.order-details-items.invoice').length) {
                $target_ = $('.column.main').find('.nav.item.current');
                $('.order-details-items.invoice').insertAfter($target_);
                $('.order-details-items.invoice').css('display', 'block');
            }
        }

        if ($('body.sales-order-shipment').length && $(window).width() < 767) {
            if ($('.order-details-items.shipments').length) {
                $target_ = $('.column.main').find('.nav.item.current');
                $('.order-details-items.shipments').insertAfter($target_);
                $('.order-details-items.shipments').css('display', 'block');
            }
        }
        // end move element after order view

        //requisition

        if ($('.requisition_list-requisition-view .product-info-container > .product-checkbox').length && $(window).width() < 767) {
            $('.requisition_list-requisition-view .product-info-container > .product-checkbox').remove();
        }
        if ($('.requisition_list-requisition-view #form-requisition-list .item > .col.qty').length && $(window).width() < 767) {
            $('.requisition_list-requisition-view .product-info-container > .product-checkbox').remove();
        }

        $(document).find('.product-item-brand').each(function() {
            jQuery(".product-item-brand").filter(function() {
                return jQuery(this).text() === "false";
            }).addClass('oneitem');
        });

        var trs = $('.checkout-cart-index .cart-empty');
        if (trs.length > 0) {
            $('.checkout-cart-index').addClass('empty');
        } else {
            $('.checkout-cart-index').removeClass('empty');
        }

        jQuery(document).on('click', 'button#btn-minicart-close', function() {
            jQuery('body').click();
        });

        // Custom minus,plus qty button
        // Change Qty Input
        var formcart = $(".form.form-cart-qty");

        formcart.on('click', '[data-update="updatecart"]', function(event) {
            event.stopPropagation();

            var input = $(this).parent().find("input");
            var value = parseInt(input.val());

            if ($(this).hasClass('minus') && value > 1) {
                $(this).parent().addClass('disable');
                input.val(value - 1);
                input.change();
            }

            if ($(this).hasClass('plus')) {
                $(this).parent().addClass('disable');
                input.val(value + 1);
                input.change();
            }

        });

        function _initDisable(elm) {
            $(elm).prop('disabled', true);
        }

        function _initEnable(elm) {
            $(elm).prop('disabled', false);
        }

        jQuery(document).on('click', '.block-addbysku .fieldset .fields .field.qty .control', function() {
            jQuery(this).addClass('active');
        });
        // end add class sticky


        // mobile menu function click
        $(document).click(function(event) {
            $target = $(event.target);
            if ($(window).width() < 992) {
                $('.megamenu-dropdown .level1 .left p').addClass('title');
                $('.ammenu-menu-wrapper.-mobile.open-submenu .megamenu-dropdown .level1 ul').hide();
                if ($target.closest('.level1').length && $target.hasClass('title')) {

                    $('.ammenu-menu-wrapper.-mobile.open-submenu .megamenu-dropdown .level1 ul').slideUp();

                    if ($target.hasClass('open')) {
                        $('.megamenu-dropdown .level1').each(function() {
                            $(this).find('p.title').removeClass('open');
                            $(this).removeClass('open');
                        });
                        $target.removeClass('open');
                        $target.parents('.level1').removeClass('open');
                        $target.parents('.level1').find('ul').slideUp();
                    } else {
                        $('.megamenu-dropdown .level1').each(function() {
                            $(this).find('p.title').removeClass('open');
                            $(this).removeClass('open');
                        });
                        $target.addClass('open');
                        $target.parents('.level1').addClass('open');
                        $target.parents('.level1').find('ul').slideDown();
                    }
                }

                if ($target.hasClass('submenu-label')) {
                    $target.closest('.ammenu-submenu-container.ammenu-submenu').css('display', 'none');
                }

                if ($target.closest('.ammenu-item.category-item').length) {
                    if (!$('.ammenu-menu-wrapper.-mobile').hasClass('open-submenu')) {
                        $('.ammenu-menu-wrapper.-mobile').addClass('open-submenu');
                    }
                }

            }

        });
        // end mobile menu function click


        //tab about us page
        $(document).find('.about-tab ul li').each(function(i, obj) {
            $(obj).removeClass('active');
            var href = (window.location.pathname).split('/');
            if ($(obj).data('href') == href[1]) {
                $(obj).addClass('active');
            }
        });
        $(document).find('.about-tab .title-current span').text($(document).find('.about-tab ul li.active a').text());
        $(document).on('click', '.about-tab .title-current', function() {
            $(this).toggleClass('open-tab');
            $(this).next().toggleClass('open-tab');

        });
        //in the media popup
        $(document).find('.row-media .item-media a').on('click', function(e) {
            e.preventDefault();
            console.log($(this).find('[data-element="desktop_image"]').attr('src'));
            $('.popup-media .popup-item .popup-image img').attr('src', $(this).find('[data-element="desktop_image"]').attr('src'));
            $('.popup-media').addClass('open-popup');
        });
        $(document).find('.popup-media .close-icon').on('click', function(e) {
            e.preventDefault();
            $('.popup-media').removeClass('open-popup');
        });

        $('.testi-list .widget.block ').slick({
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: 1,
            slidesToScroll: 1
        });

        $('.checkout-cart-index .block.crosssell .product-items').slick({
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 4,
            responsive: [{
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                }
            ]
        });
        //breadcrumb-about
        $('.cms-who-we-are .breadcrumb-about').append($('.breadcrumbs'));

        $('.cms-in-the-media .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-meet-our-brands .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-why-choose-us .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-what-we-do .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-join-the-team .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-meet-the-team .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-become-a-stockist .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-feed-fortify-finish .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-advanced-nutrition-programme .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-environ-skin-care .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-jane-iredale .breadcrumb-about').append($('.breadcrumbs'));
        $('.cms-testimonials .breadcrumb-about').append($('.breadcrumbs'));
        //menu connect function click
        $('.col-md-3.connect .item-custom .description').hide();
        $('.col-md-3.connect .item-custom:first-of-type .description').show();
        $('.col-md-3.connect .item-custom:first-of-type h5').addClass('active');
        $('.col-md-3.connect .item-custom h5').click(function() {
            $(this).next().slideToggle();
            $(this).toggleClass('active');
            $(this).parent().siblings().find('.description').slideUp();
            $(this).parent().siblings().find('h5').removeClass('active');
        });
        //footer toggle
        if ($(window).width() < 767) {
            $('.footer.content .footer-middle .col-md-3 ul').hide();
            $('.footer.content .footer-middle h3').click(function() {
                $(this).next().slideToggle();
                $(this).toggleClass('active');
                $(this).parent().toggleClass('active');
                $(this).parent().siblings().find('ul').slideUp();
                $(this).parent().siblings().find('h3').removeClass('active');
            })
        }

        //form search
        $('.clear-cross.clear-query-autocomplete').on('click', function() {
            if ($(this).val().length < 1) {
                $('body').removeClass('click-search');
            }
        });
        // $('.form.form-login #checkbox').change(function() {
        //     if (this.checked) {
        //         $('.action.login.primary').removeAttr("disabled");
        //         $('.mage-error-custom').remove();
        //     }
        // });
        // $(".form.form-login").submit(function(event) {
        //     if (!$('#checkbox').is(":checked")) {
        //         event.preventDefault();
        //         if (!$(document).find('.field.choice.required .mage-error-custom').length) {
        //             $('.field.choice.required').append("<div class='mage-error-custom' id='email-error-custom'>This is a required field.</div>");
        //         }
        //     }
        // });
        $(function() {
            enable_cb();
            $("#checkbox").click(enable_cb);
          });
        
        function enable_cb() {
        if (this.checked) {
            $("button.group1").removeAttr("disabled");
        } else {
            $("button.group1").attr("disabled", true);
        }
        }


        jQuery('button#btn-minicart-close').on('click', function() {
            jQuery('body').click();
        });

        // home logout
        if ($(window).width() < 767) {
            $('.sign-out-brand .pagebuilder-column-group').slick({
                dots: true,
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                centerMode: true,
                centerPadding: '14.6%',
            });
            $('.dropdown-icon').click(function() {
                $(this).closest('.order-history-block').toggleClass('active');
            })
            var cdf = $('.logged.catalog-category-view .block-title.filter-title');
            var ocdf = $('.logout.catalog-category-view .block-title.filter-title');
            if (cdf.offset() !== undefined) {
                var distances = $('.catalog-category-view .block-title.filter-title').offset().top - 129,
                    $windows = $(window);
                $windows.scroll(function() {
                    if ($windows.scrollTop() >= distances) {
                        $('.catalog-category-view').addClass('add-sticky');
                        $('.catalog-category-view .block-title.filter-title').addClass('st-sticky');
                    } else {
                        $('.catalog-category-view .block-title.filter-title').removeClass('st-sticky');
                        $('.catalog-category-view').removeClass('add-sticky');
                    }
                });
            }
            if (ocdf.offset() !== undefined) {
                var distances = $('.logout.catalog-category-view .block-title.filter-title').offset().top - 66,
                    $windows = $(window);
                $windows.scroll(function() {
                    if ($windows.scrollTop() >= distances) {
                        $('.catalog-category-view').addClass('add-sticky');
                        $('.catalog-category-view .block-title.filter-title').addClass('st-sticky');
                    } else {
                        $('.catalog-category-view .block-title.filter-title').removeClass('st-sticky');
                        $('.catalog-category-view').removeClass('add-sticky');
                    }
                });
            }
        }
        $(".sign-out-new-product .widget-product-carousel").slick({
            dots: true,
            infinite: false,
            slidesToShow: 5,
            slidesToScroll: 5,
            responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: false
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        initialSlide: 32
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                }
            ]
        });

        $(".sign-in-new-product .widget-product-grid").slick({
            dots: true,
            arrows: true,
            infinite: false,
            slidesToShow: 3,
            slidesToScroll: 3,
            responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        infinite: true,
                        dots: false
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                }
            ]
        });

        if ($('.catalogsearch-result-index .search.results').length > 0) {
            $('body').addClass('available');
        } else {
            $('body').removeClass('available');
        }
        /* change qty on product list */
        $('body').on('click', '.minus', function() {
            let input = $(this).parent().find('input[name="qty"]');
            let count = parseInt(input.val()) - 1;
            count = count < 1 ? 1 : count;
            input.val(count);
            input.change();
            return false;
        });

        $('body').on('click', '.plus', function() {
            let input = $(this).parent().find('input[name="qty"]');
            let count = parseInt(input.val()) + 1;
            input.val(count);
            input.change();
            return false;
        });

        /* select options configurable product on product list */
        $('.attribute-options select').change(function() {
            var optionId = $(this).val();
            var attributeId = $(this).attr('data-attribute');
            var attributeCode = $(this).attr('data-attribute-code');
            if (attributeCode == 'product_type_swatch') {
                $(this).closest('.product-item-details').find('.swatch-attribute[data-attribute-id="' + attributeId + '"]')
                    .find('.swatch-option[data-option-id="' + optionId + '"]').trigger("click");
            } else {
                $(this).closest('.product-item-details').find('.swatch-attribute[data-attribute-id="' + attributeId + '"]')
                    .find('.swatch-select').val(optionId).trigger('change');
            }

        });

        var heightSearch = $('.catalogsearch-result-index .page-title-wrapper h1').height();
        if (heightSearch > 37) {
            $('.catalogsearch-result-index').addClass('change-height');
        } else {
            $('.catalogsearch-result-index').removeClass('change-height');
        }

        /*back link 404*/
        $(".content-404 a").click(function(event) {
            event.preventDefault();
            history.back(1);
        });
        /*sidebar my account mobile*/
        if ($(window).width() < 768) {
            var text = jQuery('.sidebar-main .block-collapsible-nav .block-collapsible-nav-content .nav.item.current strong').text();
            jQuery('.sidebar-main .block-collapsible-nav .block-collapsible-nav-title strong').text(text);
            jQuery('.account .page-title-wrapper').insertBefore(jQuery('.account .column.main'));
            jQuery('.account.requisition_list-requisition-view .requisition-tilte-box').insertBefore(jQuery('.account .column.main'));
            jQuery('.account  .page-title-wrapper .order-date').insertAfter(jQuery('.account .sidebar-main'));
            jQuery('.account  .page-title-wrapper .order-actions-toolbar').insertAfter(jQuery('.account .sidebar-main'));
            jQuery('.account.requisition_list-requisition-view .requisition-view-links').insertBefore(jQuery('.account .sidebar-main'));
        }
        if ($(window).width() <= 991 & $(window).width() >= 768) {
            var text = jQuery('.sidebar-main .block-collapsible-nav .block-collapsible-nav-content .nav.item.current strong').text();
            jQuery('.sidebar-main .block-collapsible-nav .block-collapsible-nav-title strong').text(text);
        }

        /* product page */
        // add class alert out of stock
        let alert = $('.catalog-product-view').find('.alert-out-stock');
        if (alert.length) {
            alert.parents('.product-add-form').addClass('alert-stock');
        }
        /* end */


        if (jQuery('.account .sidebar .block-collapsible-nav-content .item .ewayrapid-creditcards ').length) {
            jQuery('.account .sidebar .block-collapsible-nav-content .item .ewayrapid-creditcards').closest('.account .sidebar .block-collapsible-nav-content .item').addClass('save-cards');
        }
        if (jQuery('.account .sidebar .block-collapsible-nav-content .item .promo-rewards ').length) {
            jQuery('.account .sidebar .block-collapsible-nav-content .item .promo-rewards').closest('.account .sidebar .block-collapsible-nav-content .item').addClass('promo');
        }
        if (jQuery('.account .sidebar .block-collapsible-nav-content .item .loyalty-rewards ').length) {
            jQuery('.account .sidebar .block-collapsible-nav-content .item .loyalty-rewards').closest('.account .sidebar .block-collapsible-nav-content .item').addClass('loyalty');
        }
        if (jQuery('.account .sidebar .block-collapsible-nav-content .item .store-credit ').length) {
            jQuery('.account .sidebar .block-collapsible-nav-content .item .store-credit').closest('.account .sidebar .block-collapsible-nav-content .item').addClass('store');
        }
        if (jQuery('.account .sidebar .block-collapsible-nav-content .item .company-profile ').length) {
            jQuery('.account .sidebar .block-collapsible-nav-content .item .company-profile').closest('.account .sidebar .block-collapsible-nav-content .item').addClass('profile');
        }
        function alignHeight(selector) {
            jQuery(selector).css('height', '');
            var minHeight = 0;
            jQuery(selector).each(function () {
                if (minHeight < jQuery(this).outerHeight()) {
                    minHeight = jQuery(this).outerHeight();
                }
            });
            if (minHeight > 0) {
                jQuery(selector).css('height', minHeight);
            }
        }
        if ($('.checkout-index-index').length) {
            setInterval(function(){
                alignHeight('.checkout-index-index .opc-wrapper .checkout-shipping-address .shipping-address-item');
                jQuery(window).resize(function() {
                    alignHeight('.checkout-index-index .opc-wrapper .checkout-shipping-address .shipping-address-item');
                });
            }, 2000);
        }
        setTimeout(function() {
            alignHeight('.cms-index-index .product-item-details .product-item-brand');
            alignHeight('.cms-index-index .product-item-details .swatch-custom');
            alignHeight('.cms-index-index .product-item-details .product-item-name a');
            alignHeight('.products-grid .product-item-details .product-item-brand');
        }, 500);
        jQuery(window).resize(function() {
            setTimeout(function() {
                alignHeight('.cms-index-index .product-item-details .product-item-brand');
	            alignHeight('.cms-index-index .product-item-details .swatch-custom');
	            alignHeight('.cms-index-index .product-item-details .product-item-name a');
                alignHeight('.products-grid .product-item-details .product-item-brand');
            }, 500);
        }); 
    });
});

define([
    'jquery'
], function ($) {

    $.widget('amasty.megaMenu', {
        options: {
            hambStatus: 0,
            desktopStatus: 0,
            stickyStatus: 0,
        },
        classes: {
            opened: '-opened',
            noScroll: '-am-noscroll'
        },

        _create: function () {
            var self = this,
                isMobile = $(window).width() <= 767 ? 1 : 0,
                isDesktop = self.options.desktopStatus,
                isHamb = self.options.hambStatus,
                isSticky = self.options.stickyStatus;

            $('[data-ammenu-js="menu-toggle"]').off('click').on('click', function () {
                self.toggleMenu();
            });
            
            $('.-mobile .ammenu-link.-parent').on('click', function(e){
                e.preventDefault();
                var parent = $(this).closest('.ammenu-item');
                parent.find('.ammenu-toggle').click();
            });
            
            $('.-mobile .ammenu-link.-parent').each(function(){
                var menu_label = $(this).find('.ammenu-wrapper').html();
                var menu_parent = $(this).closest('.ammenu-item');
                menu_parent.find('.ammenu-submenu-container').prepend('<div class="submenu-label">' + menu_label + '</div>');
            });
            
            if ($('.service-label-mobile').length && $('.sub-services-content').length){
                var service_label = $('.service-label-mobile').html();
                $('.sub-services-content').prepend('<div class="service-label">' + service_label + '</div>');
            }
            $('.mobile-top-block .service-label-mobile').on('click', function(){                
                $('.ammenu-menu-wrapper.-mobile').addClass('open-submenu');
            });
            
            $('.mobile-top-block .service-label').on('click', function(){               
                $('.ammenu-menu-wrapper.-mobile').removeClass('open-submenu');
            }); 
            
            $('.ammenu-submenu-container .submenu-label').on('click', function(){               
                var sub_menu_parent = $(this).closest('.ammenu-item');
                sub_menu_parent.find('.ammenu-toggle').click();
                var mobile_menu = $(this).closest('.ammenu-menu-wrapper.-mobile');
                mobile_menu.removeClass('open-submenu');
            }); 
            
            $('.ammenu-menu-wrapper.-mobile .ammenu-submenu-container').each(function(){
                var parent = $(this).closest('.ammenu-item');
                var item_url = parent.find('.ammenu-link').attr('href');
                parent.find('.ammenu-submenu-container .pagebuilder-column-group').prepend('<div class="ammenu-submenu-viewall"><a href="' + item_url + '">View All</a></div>');
            });
            
            $('.ammenu-menu-wrapper.-mobile .ammenu-submenu-container .col-submenu [data-content-type="text"]').each(function(){
                var sub_content = $(this).html();
                var sub_label = $(this).find('p:first-child strong').html();
                $(this).html('<div class="sub-sub-lable">' + sub_label + '</div><div class="sub-sub-content">' + sub_content + '</div>');
            });
            
            $('.ammenu-menu-wrapper.-mobile.open-submenu .megamenu-dropdown .level1 .title').on('click', function(){
                var parent = $(this).closest('.ammenu-menu-wrapper.-mobile.open-submenu .megamenu-dropdown .level1');
                if (parent.hasClass('open')){
                    parent.removeClass('open');
                    parent.find('ul').slideUp();
                } else {
                    $('.ammenu-menu-wrapper.-mobile.open-submenu .megamenu-dropdown .level1').removeClass('open');
                    $('.ammenu-menu-wrapper.-mobile.open-submenu .megamenu-dropdown .level1 ul').slideUp();
                    parent.addClass('open');
                    parent.find('ul').slideDown();    
                }
            });
            $('.ammenu-menu-wrapper.-mobile .ammenu-toggle').click(function(){
                var toggle_parent = $(this).closest('.ammenu-item');
                if (toggle_parent.find('.ammenu-submenu-container').hasClass('-collapsed')){
                    $('.ammenu-content .-mobile').addClass('open-submenu');
                } else {
                    $('.ammenu-content .-mobile').removeClass('open-submenu');
                }
                $('.ammenu-nav-sections.nav-sections .ammenu-content').scrollTop(0);                
            });
            if (!isHamb) {
                $('[data-ammenu-js="menu-overlay"]').on('swipeleft click', function () {
                    self.toggleMenu();
                });

                $('[data-ammenu-js="tab-content"]').on('swipeleft', function () {
                    self.toggleMenu();
                });

                if (isMobile) {
                    $(window).on('swiperight', function (e) {
                        var target = $(e.target);

                        if (e.swipestart.coords[0] < 50
                            && !target.parents().is('.ammenu-nav-sections')
                            && !target.is('.ammenu-nav-sections')) {
                            self.toggleMenu();
                        }
                    });
                }
            }

            // if (isDesktop && isSticky) {
            //     var menu = $('[data-ammenu-js="desktop-menu"]'),
            //         menuOffsetTop = menu.offset().top;

            //     $(window).on('scroll', function () {
            //         menu.toggleClass('-sticky', window.pageYOffset >= menuOffsetTop);
            //     });
            // }

            this.removeEmptyPageBuilderItems();
        },

        toggleMenu: function () {
            $('body').toggleClass(this.classes.noScroll);
            $('[data-ammenu-js="menu-toggle"]').toggleClass('-active');
            $('[data-ammenu-js="desktop-menu"]').toggleClass('-hide');
            $('[data-ammenu-js="nav-sections"]').toggleClass(this.classes.opened);
            
        },

        removeEmptyPageBuilderItems: function () {
            $('[data-ammenu-js="menu-submenu"]').each(function () {
                var element = $(this),
                    childsPageBuilder = element.find('[data-element="inner"]');

                if (childsPageBuilder.length) {
                    //remove empty child categories
                    $('[data-content-type="ammega_menu_widget"]').each(function () {
                        if (!$(this).children().length) {
                            $(this).remove();
                        }
                    });

                    var isEmpty = true;
                    $(childsPageBuilder).each(function () {
                        if ($(this).children().length) {
                            isEmpty = false;
                            return true;
                        }
                    });

                    if (isEmpty) {
                        element.remove();
                    }
                }
            });
            
            $('.ammenu-menu-wrapper.-mobile .ammenu-item.-main').each(function() {
                if (!$(this).find('.ammenu-submenu > div > div[data-element="inner"]').children().length) {
                    $(this).addClass('no-child');
                }
            });
        }
    });

    return $.amasty.megaMenu;
});

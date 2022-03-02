(function ($, root, undefined) {

    $.fn.loadDependencies = function () {

        var hasDeps = $('.has-deps');

        if (hasDeps) {
            hasDeps.each(function (index, el) {
                var modules = $(el).data('deps');
                var hasDepsPath = $(el).data('deps-path');
                var depsPath = hasDepsPath ? window[hasDepsPath] : window['fl1_ajax_object'];
                
                if (modules && depsPath instanceof Object) {
                    $.each(modules, function (key, values) {
                        $.each(values, function (index, value) {
                            switch (key) {
                                case 'js':
                                    scripts = $.getScript(depsPath.jsPath + value + '.min.'+key);
                                    break;
                            }
                        });
                    });
                }
            
            });
        }

    }

    /**
     * Chosen Select
     * 
     * @dependency chosen.js
     */
    $.fn.chosenSelect = function () {

        var chosen = $('.chosen-select').chosen({
            placeholder_text_single: 'Any',
            allow_single_deselect: true,
            inherit_select_classes: true
        });

    }

    /**
     * Resize text area
     */
    $.fn.resizeTextArea = function () {

        $.each($('textarea[data-autoresize]'), function () {
            var offset = this.offsetHeight - this.clientHeight;

            var resizeTextarea = function (el) {
                $(el).css('height', 'auto').css('height', el.scrollHeight + offset);
            };
            $(this).on('keyup input', function () {
                resizeTextarea(this);
            }).removeAttr('data-autoresize');
        });

    }

    /**
     * Tooltips
     * 
     * @dependency tooltipster
     */
    $.fn.tooltips = function () {

        // Tooltips.
        $('.tooltip:not(.tooltipstered)').tooltipster({
            theme: 'tooltipster-dark',
            contentAsHTML: true,
            contentCloning: true,
            maxWidth: 450,
            functionPosition: function (instance, helper, position) {

                // var elementID = instance.__Content[0].id;

                // if (elementID && (elementID === 'tooltip_user_menu' || elementID === 'tooltip_notifications')) {
                //     position.coord.left -= 30;
                // }

                // return position;
            },
            functionInit: function (instance, helper) {
                var $origin = $(helper.origin);
                var dataOptions = $origin.attr('data-tooltipster');

                // Override options via data attribute.
                if (dataOptions) {

                    dataOptions = JSON.parse(dataOptions);

                    $.each(dataOptions, function (name, option) {
                        instance.option(name, option);
                    });


                }
            }
        });

    }

    /**
     * useLocalStorage()
     * 
     * Helper CRUD function to handle localStorage
     * 
     * @param {string} item 
     * @param {mixed} value 
     * @returns void
     */
    $.fn.useLocalStorage = function (item, value) {

        return {
            exists: function() { localStorage.getItem(item) ? true : false},
            get: function() { localStorage.getItem(item)},
            remove: function() { localStorage.removeItem(item)},
            set: function() { localStorage.setItem(item, value)},
        }

    }

    /**
     * Converts URL parameters to object
     * 
     * @param {string} query 
     */
     $.fn.paramsToJSON = function(query) {

        query = query.substring(query.indexOf('?') + 1);
    
        var re = /([^&=]+)=?([^&]*)/g;
        var decodeRE = /\+/g;
    
        var decode = function (str) {
            return decodeURIComponent(str.replace(decodeRE, " "));
        };
    
        var params = {}, e;
        while (e = re.exec(query)) {
            var k = decode(e[1]), v = decode(e[2]);
            if (k.substring(k.length - 2) === '[]') {
                k = k.substring(0, k.length - 2);
                (params[k] || (params[k] = [])).push(v);
            }
            else params[k] = v;
        }
    
        var assign = function (obj, keyPath, value) {
            var lastKeyIndex = keyPath.length - 1;
            for (var i = 0; i < lastKeyIndex; ++i) {
                var key = keyPath[i];
                if (!(key in obj))
                    obj[key] = {}
                obj = obj[key];
            }
            obj[keyPath[lastKeyIndex]] = value;
        }
    
        for (var prop in params) {
            var structure = prop.split('[');
            if (structure.length > 1) {
                var levels = [];
                structure.forEach(function (item, i) {
                    var key = item.replace(/[?[\]\\ ]/g, '');
                    levels.push(key);
                });
                assign(params, levels, params[prop]);
                delete(params[prop]);
            }
        }
        return params;
    }

    /**
     * Converst a serialised array
     * into a JSON Object
     */
     $.fn.JSONserialize = function () {

        var unindexed_array = this.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i){
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }

    /**
     * Converts JSON object to URL
     * params string
     * 
     * @param obj data 
     */
    $.fn.JSONparamify = function(data) {
        return new URLSearchParams(data).toString();
    }

    /**
     * Utility to handle local storage
     */
    $.fn.jsStorage = function (item, data) {

        return {

            exists: function exists() { 
                return localStorage.getItem(item) ? true : false;
            },

            set: function set() { 
                localStorage.setItem(item, JSON.stringify(data));
            },

            get: function get() { 
                return JSON.parse(localStorage.getItem(item));
            },

            getProp: function getProp(prop) {
                return this.get(item)[prop];
            },

            remove: function remove() { 
                localStorage.removeItem(item);
            }

        }
    }

    /**
     * Outputs skeleton markup
     * as content preloader
     * 
     * @param {string} markup
     * @param {int} rows
     */
    $.fn.skeleton = function(markup, rows) {

        var skeleton = '';
        rows = rows ? rows : 7;

        for (i = 1; i < rows; i++) {
            skeleton += markup;
        }

        return skeleton;

    }

    /**
     * Spinner
     * 
     * @param {string} widthHeight 
     * @returns {string}
     */
    $.fn.spinner = function(widthHeight = '10') {

        return '<svg version="1.1" id="spinner" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="'+widthHeight+'px" height="'+widthHeight+'px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve"> <path opacity="0.2" fill="#FFFFFF" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946 s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634 c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/> <path fill="#FFFFFF" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0 C22.32,8.481,24.301,9.057,26.013,10.047z"> <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.5s" repeatCount="indefinite"/> </path> </svg>';

    }

    /**
     * Sticky menu
     * 
     */
    $.fn.stickyMenu = function() {

        var $ = jQuery;
    
        // Hide header on scroll down
        var didScroll;
        var lastScrollTop = 0;
        var navbarHeight = $('header.header').outerHeight();
        var delta = navbarHeight;
    
        $(window).scroll(function (event) {
            didScroll = true;
        });
    
        setInterval(function () {
            if (didScroll) {
                hasScrolled();
                didScroll = false;
            }
        }, 250);
    
        function hasScrolled() {
            var st = $(this).scrollTop();
    
            // Make scroll more than delta
            if (Math.abs(lastScrollTop - st) <= delta)
                return;
    
            // If scrolled down and past the navbar, add class .nav-up.
            if (st > lastScrollTop && st > navbarHeight) {
                // Scroll Down
                $('header.header').removeClass('sticky reset').addClass('not-sticky');
    
            } else {
                if (st >= 0 && st <= 150) {
                    $('header.header').addClass('reset').removeClass('not-sticky sticky');
    
                } else if (st + $(window).height() < $(document).height()) {
                    $('header.header').removeClass('not-sticky reset').addClass('sticky');
                }
            }
    
            lastScrollTop = st;
        }
    
    }

    /**
     * Smooth scroll
     */
    $.fn.smoothScroll = function() {
        
        $('.scroll').click(function (e) {
            e.preventDefault();
            var elementClicked = $(this).attr("href");
            var destination = $(elementClicked).offset().top;

            $("html:not(:animated),body:not(:animated)").animate({
                scrollTop: destination - 170
            }, 800);
        });

    }

    /**
     * lazyLoad
     * 
     * @dependency B-lazy.js
     */
     $.fn.lazyLoad = function() {
        
        var bLazy = new Blazy({
            selector: '.blazy'
        });
    
    }
    
    /**
     * mobileMenu
     * 
     * @dependency mmenu.js
     */
     $.fn.mobileMenu = function(selector = 'nav#nav_mobile', side = 'left') {
        
        var mmenu = $(selector).mmenu({
            "extensions": [
                "effect-menu-fade"
            ],
            "offCanvas": {
                "position": side
            },
            "navbar": {
                "title": ""
            }
        })
    
    }


    /**
     * Turns footer columns into an
     * accordion on mobile
     */
    $.fn.footerAccordion = function(breakpoint = 1024) {

        var winIsSmall;

        $('footer article.footer__menu').find('h5').click(function () {
            if (winIsSmall) {
                $(this).toggleClass('active');
                $(this).children().toggleClass('ion-ios-plus-empty ion-ios-minus-empty');
                $(this).parent().find('ul').stop().slideToggle(100);
            }
        });

        $(window).on('load resize', function () {
            winIsSmall = window.innerWidth <= breakpoint;
            $('footer article.footer__menu ul').toggle(!winIsSmall);
        });

    }

}(jQuery));
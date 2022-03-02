/*
------------------------------------------------
   ______                                 __
  / ____/___ __________  __  __________  / /
 / /   / __ `/ ___/ __ \/ / / / ___/ _ \/ /
/ /___/ /_/ / /  / /_/ / /_/ (__  )  __/ /
\____/\__,_/_/   \____/\__,_/____/\___/_/

------------------------------------------------
Carousel
*/

jQuery(document).ready(function($){

    $('.carousel_images').slick({
        dots: true,
        infinite: false,
        speed: 300,
        slidesToShow: 5,
        responsive : [
            {
                breakpoint:1200,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint:900,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 600,
                settings: {
                  slidesToShow: 2,
                  slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                }
            }
        ]
    });

    $('.testimonials-carousel').on('init', function(slick) {
        var testims = $('.testimonials__wrapper article .testim__content');
        if(testims.length > 0) {
            testims.each(function(index, testim) {
                var testimH = $(testim).outerHeight()
                if(testimH > 120) {
                    $(testim).addClass('trunc')
                    $(testim).closest('.inner').append('<span class="testim__read-more">+ read more</span>')
                }
            });
        }
    });

    $(document).on('click', '.testim__read-more', function() {
        $(this).prev().toggleClass('trunc');
        if($(this).prev().hasClass('trunc')) {
           $(this).text('+ read more')
        } else {
            $(this).text('- read less')
        }
    })

    $('.testimonials-carousel').slick({
        dots: true,
        infinite: false,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: false,
        responsive : [
            {
                breakpoint: 700,
                settings: {
                  slidesToShow: 2,
                  slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                }
            }
        ]
    });

    $('.grid-boxes-carousel').slick({
        dots: true,
        infinite: false,
        speed: 300,
        slidesToShow: 3,
        arrows: false,
        rows: 0,
        responsive : [
            {
                breakpoint: 600,
                settings: {
                  slidesToShow: 2,
                  slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                }
            }
        ]
    });

    $('.fc-layout-carousel').slick({
        dots: true,
        infinite: false,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: false,
        arrows: true,
        fade: false,
        adaptiveHeight: true,
        cssEase: 'cubic-bezier(0.645, 0.045, 0.355, 1)'
    });

});

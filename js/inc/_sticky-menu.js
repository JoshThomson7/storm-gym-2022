function sticky_menu() {

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
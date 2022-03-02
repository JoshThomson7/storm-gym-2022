// JS Awesomeness

/*
-------------------------------------------
    ____           __          __
   /  _/___  _____/ /_  ______/ /__  _____
   / // __ \/ ___/ / / / / __  / _ \/ ___/
 _/ // / / / /__/ / /_/ / /_/ /  __(__  )
/___/_/ /_/\___/_/\__,_/\__,_/\___/____/

-------------------------------------------
*/

// Libs
// @codekit-prepend "../lib/tooltipster/js/_tooltipster.bundle.min.js";
// @codekit-prepend "../lib/mmenu/js/_mmenu.js";
// @codekit-prepend "../lib/slick/js/_slick.min.js";
// @codekit-prepend "../lib/lightgallery/js/_lightgallery.js";
// @codekit-prepend "../lib/lightslider/js/_lightslider.js";
// @codekit-prepend "../lib/chosen/js/_chosen.jquery.min.js";
// @codekit-prepend "../lib/blazy/_blazy.min.js";
// @codekit-prepend "../lib/isotope/_imagesloaded.pkgd.min.js";
// @codekit-prepend "../lib/isotope/_isotope.pkgd.min.js";
// @codekit-prepend "../lib/modal-video/js/_modal-video.min.js";

// Includes
// @codekit-prepend "./inc/_helpers.js";
// @codekit-prepend "./inc/_widget-filterify.js";

// Modules
// @codekit-prepend "../modules/advanced-video-banners/js/_avb.js";
// @codekit-prepend "../modules/flexible-content/js/_flexible-content.js";
// @codekit-prepend "../modules/blog/js/_blog.js";

jQuery(function($) {

    $().loadDependencies();
    $().tooltips();
    $().stickyMenu();
    $().mobileMenu();
    $().smoothScroll();
    $().chosenSelect();
    $().footerAccordion();
    $().lazyLoad();

});

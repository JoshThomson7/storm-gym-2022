/*
    Masonry
*/

jQuery(document).ready(function($){

    var $grid = $('.masonry').imagesLoaded( function() {
        // init Isotope after all images have loaded
        $grid.isotope({
            itemSelector: '.masonry__item'
        });
    });

});

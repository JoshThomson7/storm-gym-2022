/*
------------------------------------------------
   ______      ____
  / ____/___ _/ / /__  _______  __
 / / __/ __ `/ / / _ \/ ___/ / / /
/ /_/ / /_/ / / /  __/ /  / /_/ /
\____/\__,_/_/_/\___/_/   \__, /
                         /____/
------------------------------------------------
Gallery
*/
jQuery(document).ready(function($){
	$(".fc_gallery .gallery__images").lightGallery({
        hash: false,
        download: false
    });

    var gallery = $('.gallery__images.gallery__carousel');
    var galleryItems = 6;
    var inSidebar = gallery.closest('.apm__content--content');

    if(gallery.length > 0) {

        if(inSidebar.length > 0) {
            galleryItems = 3
        }

        $('.gallery__images.gallery__carousel').lightSlider({
            item: galleryItems,
            cssEasing: 'cubic-bezier(0.25, 0, 0.25, 1)',
            controls: true,
            prevHtml: '<i class="fas fa-chevron-left"></i>',
            nextHtml: '<i class="fas fa-chevron-right"></i>',
            pager: true,
            slideMargin: 0,
            enableDrag: false,
            responsive : [
                {
                    breakpoint: 800,
                    settings: {
                        item:1
                    }
                }
            ]
        });
    }
});

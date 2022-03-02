/**
 * Blog - JS
 * 
 * @version 1.0
 */

 (function($, root, undefined) {

	var blogFilters = $('#blog_filters').filterify({
        ajaxObject: 'fl1_blogs_ajax_object',
		ajaxAction: 'blog_filters',
		responseEl: '#blog_response',
		paginationSelector: '.ajax-pagination',
        loadMoreSelector: 'blog_viewmore',
		skeleton: {
			count: 3,
			markup: '<article class="blog__article preloader">'+
                '<div class="blog__content">'+
                    '<h5></h5>'+
                    '<h2></h2>'+
                    '<date></date>'+
                    '<p></p>'+
                    '<p class="p2"></p>'+
                    '<p class="p3"></p>'+
                    '<div class="blog__more"></div>'+
                '</div>'+
                '<figure></figure>'+
            '</article>'
		}
	});

})(jQuery, this);

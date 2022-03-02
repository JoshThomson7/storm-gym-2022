/**
 * Workouts - JS
 * 
 * @version 1.0
 */

 (function($, root, undefined) {

	var workoutFilters = $('#apt_filters').filterify({
		ajaxAction: 'apt_filter_workouts',
		responseEl: '#apt_workouts_response',
		paginationSelector: '.ajax-pagination',
		skeleton: {
			count: 7,
			markup: 
                '<article class="card workout preload w-1-3">' +
                    '<div class="card__inner">'+
                        '<a href="#"><figure></figure></a>'+
                        '<div class="workout--info pad-20">'+
                            '<h2></h2>'+
                            '<ul><li></li><li></li><li></li></ul>'+
                        '</div>'+
                    '</div>'+
                '</article>'
		}
	});

	// var filterInstance = portlFilters.filterify( "instance" );

	// $(document).on('click', '.filterify-ext-select', function() {        
	//     var field_name = $(this).data('field-name');
	//     var field_value = $(this).data('field-value');
	//     $('[name="'+field_name+'"]').val(field_value).trigger('chosen:updated');
	//     portlFilters.trigger('change');
	// });

})(jQuery, this);

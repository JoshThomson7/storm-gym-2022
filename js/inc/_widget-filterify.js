/** 
 * Filterify
 * 
 * @package fl1
 * @version 1.0
 */

 (function($, root, undefined) {

	$.widget('fl1.filterify', {

		options: {
			formEl: '',
			ajaxObject: '',
            ajaxAction: '',
			responseEl: '',
			formData: {},
			paginationSelector: '',
            loadMoreSelector: '',
            postNotIn: '',
            wpQueryArgs: {
                paged: 1
            },
            skeleton: {},
            additionalFilters: [],
        },
        
		/**
		 * Constructor
		 * 
		 * @access private
		 */
		_create: function() {
			
			// Bail early if no response element
			if(this.options.responseEl == '') {
				console.error('"responseEl" option is required. This is the element where your data will appear after ther AJAX call.');
				return;
			}

			var that = this;
			var timeout = null;

			// Set jQuery element
			this.option('formEl', this.element);

			// Check if we have URL params
			var urlParams = this._maybeGetUrlParams();
			if(urlParams) {
				this.option('formData', $().paramsToJSON(urlParams));
			} else {
				this.option('formData', this._getFormData());
			}

            // Release the Kraken
            this.fetch();

			// Listen to form changes
			this._on( this.element, {
				'change': function() {
                    that.option('formData', that._getFormData());
                    that.fetch();
				},
				'keyup': function() {
					clearTimeout(timeout);

					timeout = setTimeout(function () {
                        that.option('formData', that._getFormData());
						that.fetch();
					}, 500);
				}
			});

			// Listen to additional filters
			if(this.option('additionalFilters')) {                
				$.each(this.option('additionalFilters'), function (index, selector) {
					$(document).on('change', selector, function() {
                        that.option('formData', that._getFormData());
						that.fetch();
					});
				});
            }
            
			// Set pagination selector
			if(this.option('paginationSelector')) {                
				$(document).on('click', this.option('paginationSelector'), function(evt) {
					evt.preventDefault();
					that.option('wpQueryArgs', {
                        'paged': $(this).data('paged')
                    })
					that.fetch();
				});
			}

			// Listen to load more button
			if(this.option('loadMoreSelector')) {
				$('<div class="do-load-more"><a href="javascript:void(0)" id="' + this.option('loadMoreSelector') + '">View More</a></div>').insertAfter($(this.option('responseEl')));

				$(document).on('click', '#' + this.option('loadMoreSelector'), function(evt) {
                    
                    evt.preventDefault();
                    
                    that.option('wpQueryArgs', {
                        'post__not_in': that._postNotInStore(),
                    })

                    that.fetch('loadmore');
				});
			}
			
		},

        /**
		 * Works out AJAX object
		 * 
		 * @access private
		 */
		_ajaxObject: function() {

            var ajaxObject = this.option('ajaxObject')
            ajaxObject = ajaxObject ? window[ajaxObject] : window['fl1_ajax_object']
            return ajaxObject

        },

		/**
		 * Returns serialised form data
		 * all forms/filters
		 * 
		 * @param {string} format
		 * @access private
		 */
		_getFormData: function() {

			var formData = '';
			var allForms = [this.option('formEl')];

			if(this.option('additionalFilters')) {
				$.each(this.option('additionalFilters'), function (index, selector) {
					allForms.push($(selector));
				});
			}

			$.each(allForms, function (index, jqObject) {
				formData += '&'+jqObject.serialize();
			});

			return $().paramsToJSON(formData);

		},
		
		/**
		 * Check for URL params and
		 * return them if they exist
		 * 
		 * @access private
		 */
		_maybeGetUrlParams: function() {

			var params = window.location.search;

			if(params) {
				return params.substr(1);
			}

			return false

		},

		/**
		 * Returns serialised form data
		 * in different formats
		 * 
		 * @param {string} format
		 * @access private
		 */
		_setUrl: function(format) {
			
			var urlParams = $().JSONparamify(this.option('formData'));
			history.replaceState({ id: this.option('ajaxAction')+'_url' }, '', '?' + urlParams);

        },
        
        /**
		 * Generates store of post IDs
         * Use in conjuction with loadMore
		 */
        _postNotInStore: function() {

            var targetEls = $(this.option('responseEl')).find('article');
            var postIDs = [];

            if (targetEls.length > 0) {
                targetEls.each(function (i, el) {
                    var postID = $(el).data('post-id');
                    postIDs.push(postID);
                });
            }

            return postIDs;

        },

		/**
		 * Highlights/selects/checks form
		 * elements programatically
		 * 
		 * @param obj jsonData 
		 * @access private
		 */
		_highlightEl: function() {

			var that = this;
			
			$.each(this.option('formData'), function (name, value) {
				
				var el = that.option('formEl').find('[name="'+name+'"]');
                el = el.length > 0 ? el : that.option('formEl').find('[name="'+name+'[]"]');

				if(el.length > 0) {

                    var type = $(el)[0].tagName;

                    el.each(function(idx, itm) {
                        
                        switch (type) {
                            case 'INPUT':
                                
                                if( $(itm).is(':radio') && $(itm).val() == value ) {
                                    $(itm).prop('checked', true);
                                } 
                                
                                if( $(itm).is(':checkbox') ) {
                                    var multipleChecks = value.indexOf(',') > -1;
                                    if(multipleChecks) {
                                        var values = value.split(',');
                                        $.each(values, function(idx, val) {
                                            if(val == $(itm).val()) {
                                                $(itm).prop('checked', true);
                                            }
                                        });
                                    } else {
                                        if( $(itm).val() == value ) {
                                            $(itm).prop('checked', true);
                                        }
                                    }
                                }

                                if($(itm).is(':text')) {
                                    $(itm).val(value);
                                }
    
                                break;
    
                            case 'SELECT':
                                $(itm).val(value);
                                $(itm).trigger('chosen:updated');
                                break;
                        
                            default:
                                break;
                        }

                    });

				}

			});

		},

		/**
		 * Preloader skeleton markup
		 * 
		 * @access private
		 */
		_skeleton: function() {

			var count = this.options.skeleton.count ? this.options.skeleton.count : 4;
			var markup = this.options.skeleton.markup;

			var skeleton = '';

			if(markup) {

				for (i = 1; i < count; i++) {
					skeleton += markup;
				}

				return skeleton;

			}

			return false;

        },
        
        _spinner: function() {

            return '<svg viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg" fill="#fff" style="width:30px; margin: 0 30px;"> <circle cx="15" cy="15" r="15"> <animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /> <animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /> </circle> <circle cx="60" cy="15" r="9" fill-opacity="0.3"> <animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite" /> <animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite" /> </circle> <circle cx="105" cy="15" r="15"> <animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /> <animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /> </circle> </svg>';

        },

		/**
		 * Fetches data
		 * 
		 * @access public
		 */
		fetch: function(mode = null) {

            var that = this;
            
            /**
             * Check for mode
             */
            switch (mode) {
                case 'loadmore':
                    $('#' + that.option('loadMoreSelector')).html(that._spinner());
				    $(that.option('responseEl')).append(that._skeleton());
                    break;
            
                default:
                    $(that.option('responseEl')).html(that._skeleton());
                    that.option('wpQueryArgs', {})
                    break;
            }

            // Fire!
			$.ajax({
				url: that._ajaxObject().ajaxUrl,
				dataType: 'html',
				type: 'POST',
				data: ({
					'action': that.option('ajaxAction'),
					'ajax_security' : that._ajaxObject().ajaxNonce,
                    'formData': that.option('formData'),
                    'wpQueryArgs': this.option('wpQueryArgs')
				}),
				success: function(response) {

                    var buttonEl = $('#' + that.option('loadMoreSelector'));

					if (mode === 'loadmore') {
						if (response != '') {
							$(that.option('responseEl')).append(response);
							buttonEl.removeClass('disabled').html('View more');
							$('article.preloader').remove();
						} else {
                            buttonEl.addClass('disabled').html('End of listings');
							$('article.preloader').remove();
						}
					} else {
						$(that.option('responseEl')).html(response);
					}

                    $('article.preloader').remove();
					that._setUrl();
					that._highlightEl();
				},
				error: function(err) {
					console.error(err);
				}
			});

		}

	});

})(jQuery, this);
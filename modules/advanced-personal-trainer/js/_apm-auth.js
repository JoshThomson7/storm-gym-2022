/**
 * APM Auth
 * 
 * @package APM
 */

(function ($, root, undefined) {

    var spinner = '<svg id="spinner" class="spinner" width="20" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"> <circle class="path" fill="none" strokeWidth="6" strokeLinecap="round" cx="33" cy="33" r="30"></circle></svg>';

    $(window).on('load', function () {

        maybeClearToken();

        /**
         * Events
         */
        // Select
        $(document).on('submit', '.woocommerce-form-login', function (e) {
            
            // This code prevents form from actually being submitted
            e.preventDefault();

            var form = $(this);
            var button = form.find('.wc__form__field.submit button');
            var form_data = $(this).serialize();

            button.html(spinner)

            $.ajax({
                url: apm_ajax_object.ajax_url,
                dataType: 'json',
                type: 'POST',
                data: ({
                    'action': 'apm_set_token',
                    'ajax_security': apm_ajax_object.ajax_nonce,
                    'form_data': form_data
                }),
                success: function (response) {
                    
                    if(response instanceof Object) {

                        if(response.hasOwnProperty('code')) {

                            var errorEl = $('.login-errors');
                            var message = '';

                            switch (response.code) {
                                case '[jwt_auth] empty_username':
                                    message = 'Please enter your email address.';
                                    break;
                                case '[jwt_auth] empty_password':
                                    message = 'Please enter your password.'
                                    break;

                                case '[jwt_auth] incorrect_password':
                                case '[jwt_auth] invalid_email':
                                    message = 'Incorrect email or password.'
                                    break;
                            
                                default:
                                    break;
                            }

                            errorEl.addClass('do-error')
                            errorEl.find('li').html(message)

                        } else {

                            if(response.token && response.redirect_url) {
                                localStorage.setItem('__bodyset_wp', response.token);
                                window.location.href = response.redirect_url
                                button.html('Success...')
                            }

                        }

                    } else {
                        button.html('Sign in')
                    }

                },
                error: function (err) {
                    console.error(err);
                    button.html('Sign in')
                }
            });

        });

    });

    /**
     * Get selected filtered data and reload via AJAX
     */
    function maybeClearToken() {

        var isLoggedIn = $('body').data('logged-in');
        var token = localStorage.getItem('__bodyset_wp');

        if(isLoggedIn === false && token) {
            localStorage.removeItem('__bodyset_wp')
        }

    }


})(jQuery, this);

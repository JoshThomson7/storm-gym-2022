function gf_form_ajax(clicked_element) {
    var $ = jQuery;

    // set variables
    var gf_id = clicked_element.attr('data-gf-id');
    var event_id = clicked_element.attr('data-event-id');

    // show pop-up
    $('.gf__modal__form .wp__loading').addClass('is__loading');
    $('.gf__modal__form__overlay').addClass('open');
    $('body').addClass('no__scroll');

    // AJAX results
    $.ajax({
        url: fl1_ajax_object.ajax_url,
        dataType: 'html',
        type: 'GET',
        data: ({
            'action' : 'gf_ajax_form',
            'security' : fl1_ajax_object.ajax_nonce,
            'gf_id' : gf_id,
            'event_id' : event_id
        }),

        success: function(data) {
            $('.wp__loading').removeClass('is__loading');
            $('#gf_ajax').html(data);
            if(window['gformInitDatepicker']) {gformInitDatepicker();}
        },

        error: function(xhr, ajaxOptions, thrownError){
            alert(xhr.status);
        }
    });
}

jQuery(document).ready(function($){

    $(document).on('click', '[data-gf-ajax-trigger]', function(e) {
        e.preventDefault();

        clicked_element = $(this);
        gf_form_ajax(clicked_element);
    });

    $(document).on('click', '.gf__modal__form .close', function(e) {
        e.preventDefault();

        // hide pop-up
        $('.gf__modal__form__overlay').removeClass('open');
        $(this).closest('.flexible__content').removeAttr('style');
        $('body').removeClass('no__scroll');

        setTimeout(function() {
            // destroy form
            $('#gf_ajax').html('');
        }, 500);

    });

});

/*
-----------------------------------------------------
    ___                            ___
   /   | ______________  _________/ (_)___  ____
  / /| |/ ___/ ___/ __ \/ ___/ __  / / __ \/ __ \
 / ___ / /__/ /__/ /_/ / /  / /_/ / / /_/ / / / /
/_/  |_\___/\___/\____/_/   \__,_/_/\____/_/ /_/

-----------------------------------------------------
Accordion
*/

jQuery(document).ready(function($){

    $('h3.toggle').click(function() {

        $('.accordion__wrap').removeClass('inactive');

        var parent = $(this).parent();

        if(parent.hasClass('active')) {
            // reset current
            $(this).removeClass('active');
        } else {
            // reset all
            $('.accordion__wrap').removeClass('active');
        }

        parent.toggleClass('active');
        $(this).find('span').toggleClass( 'fa-chevron-down fa-chevron-up' );

        if($('.accordion__wrap.active').length > 0) {
            $('.accordion__wrap:not(.active)').addClass('inactive');
        }

    });

});

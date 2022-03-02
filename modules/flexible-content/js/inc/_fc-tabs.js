/*
---------------------------
  ______      __
 /_  __/___ _/ /_  _____
  / / / __ `/ __ \/ ___/
 / / / /_/ / /_/ (__  )
/_/  \__,_/_.___/____/

---------------------------
Tabs
*/

jQuery(document).ready(function($) {

    $('ul.tabbed li').click(function() {
        var parent = $(this).closest('.tabbed-wrapper');
        parent.find('ul.tabbed li').removeClass('active');
        $(this).addClass('active');
        parent.find('.tab__content').hide();
        var activeTab = $(this).find('a').attr('data-id');
        $('.' + activeTab).show();

        if(window.innerWidth <= 900) {
            var destination = $('.'+activeTab).offset().top;

            $("html:not(:animated),body:not(:animated)").animate({
                scrollTop: destination - 170
            }, 800);
        }

        return false;
    });

    var fc_tabs = $('.fc_tabs');

    fc_tabs.each(function(index, fc_tab) {
        $(fc_tab).find('.tab__content').hide();
        $(fc_tab).find('.tab__content:first').show();
        $(fc_tab).find('ul.tabbed li:first').addClass('active');
    });

});

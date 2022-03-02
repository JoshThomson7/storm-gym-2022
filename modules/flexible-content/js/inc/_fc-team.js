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

jQuery(document).ready(function($){

    $('.team__modal').click(function() {
        var team_name = $(this).attr('href');
        //console.log(team_name);

        $('body').addClass('no__scroll');
        $('header.header').addClass('not-sticky');
        $('.team__popup__holder').addClass('on');
        $(team_name).addClass('is__active');

        return false;
    });

    $('a.team__switch').click(function() {
    	if($(this).hasClass('team__next')) {
        	var next = $(this).closest('.team__popup.is__active').next('.team__popup').attr('id');
        } else {
        	var next = $(this).closest('.team__popup.is__active').prev('.team__popup').attr('id');
        }

        var current = $(this).closest('.team__popup.is__active').attr('id');

        $('#'+current).addClass('rotate__bye');
        $('#'+next).addClass('is__active rotate__hello');
        $('#'+current).removeClass('is__active');

        return false;
    });

    //close
    $('a.team__close').click(function() {
    	$('.team__popup').removeClass('is__active rotate__hello rotate__bye');
        $('.team__popup__holder').removeClass('on');
        $('body').removeClass('no__scroll');

        return false;
    });

    // $(document).mouseup(function(e) {
    //     var container = $('.team__popup');

    //     // if the target of the click isn't the container nor a descendant of the container
    //     if (!container.is(e.target) && container.has(e.target).length === 0) {
    //         $('.team__popup').removeClass('is__active rotate__hello rotate__bye');
    //         $('.team__popup__holder').removeClass('on');
    //         $('body').removeClass('no__scroll');
    //     }
    // });

});

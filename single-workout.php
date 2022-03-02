<?php
/**
 * Single workout
 */

get_header();

global $post;

AVB::avb_banners();

$vimeo = wp_remote_request('https://api.vimeo.com/videos/617819087/', array(
    'method' => 'GET',
    'headers'     => array(
        'Authorization' => 'Bearer aadce278dad76bbc264f9b3bfb608c90',
    )
));

if(!is_wp_error($vimeo)) {
    $vimeoBody = wp_remote_retrieve_body($vimeo);
    $vimeoBody = json_decode($vimeoBody);
    //pretty_print($vimeoBody);
}
?>

<iframe id="apt_workout" width="640" height="360" src="https://player.vimeo.com/video/617819087" frameborder="0" allow="autoplay; fullscreen; accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

<button class="play">Play</button><br>
<button class="pause">Pause</button><br>
Current time: <span class="cur-time"></span>

<script>
    var curTime;
    var iframe = document.getElementById('apt_workout');
    var player = new Vimeo.Player(
        iframe,
        {
            autoplay: true
        }    
    );

    player.play();

    jQuery(function($) {
        $('button.play').on('click', function() {
            player.play();
        });

        $('button.pause').on('click', function() {
            player.pause();

            player.getCurrentTime().then(function(seconds) {
                $('.cur-time').html(seconds);
            }).catch(function(error) {
                // an error occurred
            });
        });
    }); 

    // player.getVideoTitle().then(function(title) {
    //     console.log('title:', title);
    // });
</script>

<?php get_footer(); ?>

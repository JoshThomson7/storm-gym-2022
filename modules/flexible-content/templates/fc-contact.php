<?php
/*
    Contact
*/

$map = get_sub_field('contact_address');
?>

    <div class="fc_contact_wrapper">
        <article>
            <ul>
                <li class="address"><i class="fal fa-map-marker-alt fa-fw"></i> <span><?php echo str_replace(', ', '<br>', $map['address']); ?></span></li>
                <?php if(get_sub_field('contact_phone')): ?><li><i class="fal fa-phone fa-fw"></i> <span><?php the_sub_field( 'contact_phone' ); ?></span></li><?php endif; ?>
                <?php if(get_sub_field('contact_email')): ?><li><i class="fal fa-paper-plane fa-fw"></i> <span><?php echo hide_email(get_sub_field( 'contact_email' )); ?></span></li><?php endif; ?>
            </ul>
        </article>

        <article class="map">
            <script type="text/javascript">
                jQuery(document).ready(function ($) {

                    // Define the latitude and longitude positions
                    var latitude = parseFloat("<?php echo $map['lat']; ?>");
                    var longitude = parseFloat("<?php echo $map['lng']; ?>");
                    var latlngPos = new google.maps.LatLng(latitude, longitude);

                    var mapOptions = {
                        zoom: 13,
                        center: latlngPos,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,

                        zoomControl: true,
                        zoomControlOptions: {
                            style: google.maps.ZoomControlStyle.SMALL,
                            position: google.maps.ControlPosition.LEFT_BOTTOM
                        },
                        panControl: false,
                        panControlOptions: {
                            position: google.maps.ControlPosition.BOTTOM_RIGHT
                        },

                        mapTypeControl: true,
                        mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                            position: google.maps.ControlPosition.BOTTOM_CENTER
                        },

                        streetViewControl: true,
                        streetViewControlOptions: {
                            position: google.maps.ControlPosition.LEFT_BOTTOM
                        },
                        scrollwheel: false,
                        draggable: true
                    };

                    // Define the map
                    map = new google.maps.Map(document.getElementById("map_single"), mapOptions);

                    var marker_icon = new google.maps.MarkerImage("<?php echo esc_url(get_stylesheet_directory_uri()); ?>/img/marker.png", null, null, null, new google.maps.Size(50,64));

                    // Add the marker
                    var marker = new google.maps.Marker({
                        position: latlngPos,
                        map: map,
                        //icon: marker_icon
                    });

                    // Center map on resize (responsive)
                    google.maps.event.addDomListener(window, "resize", function() {
                        var center = map.getCenter();
                        google.maps.event.trigger(map, "resize");
                        map.setCenter(center);
                    });

                });
            </script>
            <div id="map_single"></div>
        </article>
    </div><!-- fc_contact_wrapper -->

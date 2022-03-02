<?php
/**
 * APM Single Location
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

global $post;
$post_id = $post->ID;

$page_caption = get_field('page_banner_caption', $post_id);

$location = new APM_Location($post_id);
$todate = new DateTime('now', wp_timezone());
$today = $todate->format('l');
?>

<section class="banners inner">
    <div class="max__width">
        <div class="banner__content">
            <h1><?php echo $location->title(); ?></h1>
            <?php if($page_caption): ?><?php echo $page_caption; ?><?php endif; ?>
        </div><!-- banner__content -->

        <div id="apm_clinic_map"></div>
    </div><!-- max__width -->
</section><!-- home__banners -->

<section class="apm__clinic--single">

    <div class="max__width">
        <div class="apm__clinic--single-content feefo">
            <?php feefo_service_rating_box($post_id); ?>
        </div>

        <aside>
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('book'))) ?>" class="button orange_solid button-single-location">Book appointment</a>
        </aside>
    </div><!-- max__width -->

    <div class="max__width">
    
        <div class="apm__clinic--single-content">
            <?php flexible_content(); ?>
        </div>

        <aside>
            <article class="primary phone">
                <p>033 0333 0435</p>
            </article>

            <?php if($location->has_location()): ?>
                <article class="secondary">
                    <p><?php echo $location->location('address', true); ?></p>
                </article>
            <?php endif; ?>

            <?php if($location->has_times()): ?>
                <article>
                    <ul class="opening-times">            
                        <?php
                            $todays_status = '';
                            foreach($location->opening_times() as $opening_time):
                                $is_today = $opening_time->weekday['is_today'];
                                $todays_status = !empty($is_today) ? $opening_time->status['text'] : $todays_status;
                        ?>
                                <li class="<?php echo $is_today.$opening_time->status['class']; ?>"><?php echo $opening_time->display; ?></li>
                        <?php endforeach; ?>
                        <li class="current-status"><?php echo $todays_status; ?></li>
                    </ul><!-- hours-table -->
                </article>
            <?php endif; ?>

            <?php if($location->fees()): ?>
                <article class="secondary">
                    <h5>Costs <figure><i class="fas fa-star"></i> Best Value</figure></h5>

                    <ul class="fees">
                        <?php
                            foreach($location->fees() as $fee):
                                
                            $highlight = '';
                            if($fee['clinic_fee_highlight']) {
                                $highlight = 'highlight';
                            }
                        ?>
                            <li class="<?php echo $highlight; ?>">
                                <span><?php echo $fee['clinic_fee_service']; ?></span>
                                <strong><?php echo $fee['clinic_fee_fee']; echo $highlight ? '<i class="fas fa-star"></i>' : ''; ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endif; ?>
        </aside>

    </div>

</section><!-- apm__clinic-single -->

<?php
    if($location->has_location()):

    $lat = $location->location('lat');
    $lng = $location->location('lng');
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {

            // Define the latitude and longitude positions
            var latitude = parseFloat("<?php echo $lat; ?>");
            var longitude = parseFloat("<?php echo $lng; ?>");
            var latlngPos = new google.maps.LatLng(latitude, longitude);

            var mapOptions = {
                zoom: 12,
                center: latlngPos,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scaleControl: true,
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
                draggable: true,
                styles: [
                    {
                        "elementType": "geometry",
                        "stylers": [
                        {
                            "color": "#f5f5f5"
                        }
                        ]
                    },
                    {
                        "elementType": "labels.icon",
                        "stylers": [
                        {
                            "visibility": "off"
                        }
                        ]
                    },
                    {
                        "elementType": "labels.text.fill",
                        "stylers": [
                        {
                            "color": "#616161"
                        }
                        ]
                    },
                    {
                        "elementType": "labels.text.stroke",
                        "stylers": [
                        {
                            "color": "#f5f5f5"
                        }
                        ]
                    },
                    {
                        "featureType": "administrative.land_parcel",
                        "elementType": "labels.text.fill",
                        "stylers": [
                        {
                            "color": "#bdbdbd"
                        }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "geometry",
                        "stylers": [
                        {
                            "color": "#eeeeee"
                        }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "labels.text.fill",
                        "stylers": [
                        {
                            "color": "#757575"
                        }
                        ]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "geometry",
                        "stylers": [
                        {
                            "color": "#e5e5e5"
                        }
                        ]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "labels.text.fill",
                        "stylers": [
                        {
                            "color": "#9e9e9e"
                        }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry",
                        "stylers": [
                        {
                            "color": "#ffffff"
                        }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "labels.text.fill",
                        "stylers": [
                        {
                            "color": "#757575"
                        }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry",
                        "stylers": [
                        {
                            "color": "#dadada"
                        }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "labels.text.fill",
                        "stylers": [
                        {
                            "color": "#616161"
                        }
                        ]
                    },
                    {
                        "featureType": "road.local",
                        "elementType": "labels.text.fill",
                        "stylers": [
                        {
                            "color": "#9e9e9e"
                        }
                        ]
                    },
                    {
                        "featureType": "transit.line",
                        "elementType": "geometry",
                        "stylers": [
                        {
                            "color": "#e5e5e5"
                        }
                        ]
                    },
                    {
                        "featureType": "transit.station",
                        "elementType": "geometry",
                        "stylers": [
                        {
                            "color": "#eeeeee"
                        }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [
                        {
                            "color": "#c9c9c9"
                        }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "labels.text.fill",
                        "stylers": [
                        {
                            "color": "#9e9e9e"
                        }
                        ]
                    }
                ]
            };

            // Define the map
            map = new google.maps.Map(document.getElementById("apm_clinic_map"), mapOptions);

            var marker_icon = new google.maps.MarkerImage(apm_ajax_object.imgPath+"/map-marker.png", null, null, null, new google.maps.Size(50,38));

            // Add the marker
            var marker = new google.maps.Marker({
                position: latlngPos,
                map: map,
                icon: marker_icon
            });

            // Center map on resize (responsive)
            google.maps.event.addDomListener(window, "resize", function() {
                var center = map.getCenter();
                google.maps.event.trigger(map, "resize");
                map.setCenter(center);
            });

            /* Streetview */
            var panorama = new google.maps.StreetViewPanorama(
                document.getElementById('street_single'), {
                    position: latlngPos,
                    pov: {
                        heading: 34,
                        pitch: 10
                    }
                }
            );

        });
    </script>
<?php endif; ?>


<?php get_footer(); ?>
<?php
// Defaults
$default_product = null;
$default_location = null;
if(is_singular('clinic')) {
    $default_product = get_field('clinic_default_product');
    $default_location = get_field('clinic_default_location');
}
?>
<div class="apm__global__search has-deps" data-deps='{"js":["apm-search"]}' data-deps-path="apm_ajax_object">

    <div class="apm__global__search__wrapper">
        <a href="#" class="apm__close"><i class="ion-android-close"></i></a>

        <div class="apm__global__search__logo">
            <?php echo file_get_contents(esc_url(get_stylesheet_directory_uri()).'/img/bodyset-logo.svg'); ?>
        </div>

        <div class="apm__global__search__tabs">
            <ul>
                <li><a href="#" class="active">Get me whatever is next available</a></li>
                <li><a href="<?php echo esc_url(home_url()); ?>/clinics/" target="_blank">Search by location</a></li>
            </ul>
        </div><!-- apm__global__search__tabs -->

        <div class="apm__global__search__form">

            <?php
                $product_services = new WP_Query(array(
                    'post_type'         => 'product',
                    'post_status'       => 'publish',
                    'posts_per_page'    => -1,
                    'product_mode'      => 'search',
                    'orderby'           => 'menu_order',
                    'order'             => 'asc'
                ));
            ?>

            <article class="apm__service" style="width: 40%;">
                <h4>1. Select a service</h4>
                <select class="apm__search__field chosen-select" data-action="service">
                    <option value="">Select a service</option>
                    <?php
                        while($product_services->have_posts()) : $product_services->the_post();

                        $selected = '';
                        if($default_product == get_the_ID()) {
                            $selected = ' selected';
                        }
                    ?>
                        <option value="<?php the_ID(); ?>"<?php echo $selected; ?>><?php the_title(); ?></option>
                    <?php endwhile; wp_reset_postdata(); ?>
                </select>

                <span class="apm__error"></span>
            </article>

            <article class="apm__location" style="display: none;">
                <article>
                    <h4>2. Location</h4>

                    <a class="apm__geolocate__global" href="javascript:void(0);" title="Use my current location" data-action="geolocate"><span class="ion-pinpoint"></span></a>

                    <input type="text" id="apm_geocode" value="<?php //echo $default_location; ?>St Martin's Le Grand, London EC1A 4EN, UK" class="apm__search__field disabled" data-action="geocode" placeholder="Please select a service first" disabled>

                    <span class="apm__error"></span>
                </article>

                <article>
                    <h4>&nbsp;</h4>
                    <select class="apm__search__field disabled chosen-select" name="radius" data-action="radius" disabled>
                        <option value="0.01">This area only</option>
                        <option value="0.402336">1/4 mile</option>
                        <option value="0.804672">1/2 mile</option>
                        <option value="1.60934">1 mile</option>
                        <option value="8.04672">5 miles</option>
                        <option value="16.0934">10 miles</option>
                        <option value="24.1402">15 miles</option>
                        <option value="32.1869">20 miles</option>
                        <option value="48.2803">30 miles</option>
                        <option value="64.3738">40 miles</option>
                        <option value="80.4672">50 miles</option>
                    </select>
                </article>
            </article>

            <article class="apm__trigger">
                <h4>&nbsp;</h4>
                <a href="#" class="apm__do__search" data-action="geocode">
                    <span>Search</span>

                    <div class="loading">
                        <div class="loader loader--style2" title="1">
                            <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="10px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">

                                <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946 s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634 c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
                                <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0 C22.32,8.481,24.301,9.057,26.013,10.047z">
                                    <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.5s" repeatCount="indefinite"/>
                                </path>
                              </svg>
                        </div><!-- loader -->
                    </div><!-- loading -->
                </a>
            </article>

            <article>
                <div class="apm__global__search__results">
                    <h4>3. Pick a time</h4>
                    <p>Please start by selecting a service.</p>
                </div><!-- apm__global__search__results -->
            </article>

        </div><!-- apm__global__search__form -->

    </div><!-- apm__global__search__wrapper -->
</div><!-- apm__global__search -->

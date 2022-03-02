<?php
/**
 * iCal Global Search Loop
 *
 * @author  Various
 * @package Advanced Physio Module
 *
*/

// reset if 'all'
$practitioner_gender = $practitioner_gender === 'all' ? null : $practitioner_gender;
// $clinic_id = $clinic_id === 'all' ? null : $clinic_id;
// $practitioner_id = $practitioner_id === 'all' ? null : $practitioner_id;

$appointments = appointments_by_radius($product_id, $lat, $lng, $radius, $slot_length, $slot_start, 10, $practitioner_gender, $clinic_ids, $practitioner_ids);

if(!empty($appointments)) { ?>

    <?php if($action != 'load_more') { echo '<div class="apm__results__appointments">'; }

        $appointment_count = 1;

        foreach($appointments as $appointment):

            // set appointment data
            $practitioner_id = $appointment['practitioner'];
            $clinic_id = $appointment['clinic'];
            $start_date = $appointment['start_datetime'];
            $end_date = $appointment['end_datetime'];
            $distance = $appointment['distance'];
			$product_tier = wp_get_post_terms($clinic_id, 'pricing_tier');
        	$product_tier_id = $product_tier[0]->term_id;

            $service_name = get_the_title($product_id);

            /**
             * Dates
            */
            // start
            $weekday = date('D', strtotime($start_date));
            $day = date('j', strtotime($start_date));
            $month = date('M', strtotime($start_date));
            $year = date('Y', strtotime($start_date));

            $slot_start = date('H:i', strtotime($start_date));
            $slot_end = date('H:i', strtotime($end_date));
			$iso_slot_start = date('Ymd\THis\Z', strtotime($start_date));
            $iso_slot_end = date('Ymd\THis\Z', strtotime($end_date));

            /**
             * Practitioner data
             */
            $attachment_id = get_field('team_member_photo', $practitioner_id);
            $practitioner_pic = vt_resize($attachment_id,'' , 400, 400, true);

            $practitioner_name = get_the_title($practitioner_id);

            /**
             * Clinic data
            */
            $clinic_title = get_the_title($clinic_id);

            $scroll_id = '';
            if($appointment_count === 1 && $action === 'load_more') {
                $scroll_id = ' id="apm__scroll__here"';
            }

            // get last slot
            if($appointment_count === 10) {
                $next_slot = date('Ymd\THis\Z', strtotime($start_date.' +'.$slot_length.' minutes'));
            }
        ?>

            <div class="apm__clinic"<?php echo $scroll_id; ?>>

                <div class="apm__clinic__details">
                    <figure>
                        <img src="<?php echo $practitioner_pic['url'] ?>" alt="<?php echo $practitioner_name; ?>">
                    </figure>

                    <div class="apm__clinic__meta">
                        <h3><?php echo $clinic_title; ?></h3>
                        <h5><?php echo $practitioner_name; ?><strong><?php echo $distance; ?> miles</strong></h5>
                    </div><!-- apm__clinic__meta -->
                </div><!-- apm__clinic__details -->

                <div class="apm__clinic__book">
                    <form action="<?php echo WC()->cart->get_cart_url(); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="add-to-cart" value="<?php echo $product_id; ?>" />
                        <input type="hidden" name="apm_service" value="<?php echo ucfirst($service_name); ?>" />
                        <input type="hidden" name="apm_practitioner" value="<?php echo $practitioner_name; ?>" />
                        <input type="hidden" name="apm_practitioner_id" value="<?php echo $practitioner_id; ?>" />
                        <input type="hidden" name="apm_clinic" value="<?php echo get_the_title($clinic_id); ?>" />
                        <input type="hidden" name="apm_clinic_id" value="<?php echo $clinic_id; ?>" />
						<input type="hidden" name="apm_price_tier" value="<?php echo $product_tier_id; ?>">
                        <input type="hidden" name="apm_slot_date" value="<?php echo $weekday.', '.$day.' '.$month.' '.$year; ?>" />
                        <input type="hidden" name="apm_slot" value="<?php echo $slot_start.' - '.$slot_end; ?>" />
                        <input type="hidden" name="apm_slot_start" value="<?php echo $iso_slot_start; ?>" />
                        <input type="hidden" name="apm_slot_end" value="<?php echo $iso_slot_end; ?>" />
                        <input type="hidden" name="apm_appointment_price" value="<?php echo get_field('clinic_physio_appointment_fee', $clinic_id); ?>" />
                        <button type="submit"><?php echo $weekday.', '.$day.' '.$month.' - '.$slot_start; ?></button>
                    </form>
                </div><!-- apm__clinic__book -->

            </div><!-- apm__clinic -->

        <?php $appointment_count++;
        endforeach;

    if($action != 'load_more') { echo '</div>'; } ?>

    <div class="apm__global__search__pager">
        <a href="#" data-action="load_more" data-slot-start="<?php echo $next_slot; ?>" class="apm__apply__filters">Load more <i class="ion-plus"></i></a>

        <div class="loader loader--style2" title="1">
            <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">

                <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946 s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634 c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
                <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0 C22.32,8.481,24.301,9.057,26.013,10.047z">
                    <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.5s" repeatCount="indefinite"/>
                </path>
              </svg>
        </div><!-- loader -->
    </div><!-- apm__global__search__pager -->

    <?php
} else { ?>

    <div class="apm__global__search__not__found">
        <h3>No appointments found.</h3>
        <p>Please try a different search.</p>
    </div><!-- apm__global__search__not__found -->

<?php } ?>

<?php
/**
 * APM Feefo
 *
 * @author  Various
 * @package Advanced Physio Module
 *
*/

class WP_Feefo {

    /**
     * Feefo API endpoint
     * 
     * @string
     */
    private $api_endpoint = 'https://api.feefo.com/api';

    /**
     * Feefo API version
     * 
     * @string
     */
    private $api_version = '10';

    /**
     * Unique merchant indetifier
     * 
     * @string
     */
    private $merchant;

    /**
     * Constructor
     */
    public function __construct($merchant = null) {

        $this->merchant = $merchant;

    }

    /**
     * Init hooks
     */
    public function init() {

        // Crons
        add_filter('cron_schedules', array($this, 'add_cron_schedules'));
        add_action('feefo_cron', array($this, 'update_ratings_via_cron'), 1, 2);

        // On save
        add_action('acf/save_post', array($this, 'on_save_feefo'), 20);

    }

    /**
     * Returns total rating
     */
    public function get_service_rating() {

        $rating = array();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->api_endpoint.'/'.$this->api_version.'/reviews/summary/all?merchant_identifier='.$this->merchant.''
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        if(!empty($response)) {

            $count = $response->meta->count;
            $average = $response->rating->rating;
            $max = $response->rating->max;

            $rating = array(
                'count' => $count,
                'average' => $average,
                'max' => $max
            );

        }
        
        return $rating;

        curl_close($curl);

    }

    /**
     * Returns service reviews
     */
    public function get_service_reviews() {

        $reviews = array();
        
        //Get reviews
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->api_endpoint.'/'.$this->api_version.'/reviews/all?merchant_identifier='.$this->merchant.''
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        if(!empty($response)) {
            $reviews = $response->reviews;
        }
        
        return $reviews;

        curl_close($curl);

    }

    /**
     * Returns product reviews
     * 
     * @param string $sku
     */
    public function get_product_rating($sku = null) {
        
        //Get reviews
        $rating = array();
        $curl = curl_init();

        if($sku) {
            $sku = '&product_sku='.$sku;
        }

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->api_endpoint.'/'.$this->api_version.'/products/ratings/?merchant_identifier='.$this->merchant.'&review_count=true'.$sku
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        if(!empty($response)) {
            $rating = $response->products[0]->rating;
            $rating = !empty($rating) && is_numeric($rating) ? $rating : '';

            $review_count = $response->products[0]->review_count;
            $review_count = !empty($review_count) && is_numeric($review_count) ? $review_count : '';

            $rating = array(
                'rating' => $rating,
                'review_count' => $review_count
            );
        }
        
        return $rating;

        curl_close($curl);

    }

    /**
     * Returns product reviews
     * 
     * @param string $sku
     */
    public function get_product_reviews($sku = null, $last_review = false) {
        
        //Get reviews
        $product_reviews = array();
        $curl = curl_init();

        if($sku) {
            $sku = '&product_sku='.$sku;
        }

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->api_endpoint.'/'.$this->api_version.'/reviews/product/?merchant_identifier='.$this->merchant.$sku
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        if(!empty($response)) {
            $reviews = $response->reviews;

            if(!empty($reviews)) {

                $review_count = 1;
                foreach($reviews as $review) {

                    $customer = $review->customer->display_name;
                    $product_review = array();

                    if(!empty($review->products)) {
                        $product_review = array(
                            'rating' => $review->products[0]->rating->rating,
                            'review_body' => $review->products[0]->review,
                            'date' => date('Y-m-d', strtotime($review->products[0]->created_at))
                        );
                    }

                    $the_review = array(
                        'customer' => $customer,
                        'product_review' => $product_review
                    );

                    array_push($product_reviews, $the_review);

                    // Only show last review
                    if($last_review) {
                        if($review_count == 1) { break; }
                    }

                    $review_count++;
                }
            }
            
        }
        
        return $product_reviews;

        curl_close($curl);

    }

    /**
     * Updates service ratings
     */
    public function update_service() {

        $service_rating = $this->get_service_rating();

        // Average
        $average = isset($service_rating['average']) ? $service_rating['average'] : '';
        if($average != '') {
            update_field('feefo_total_average', $average, 'option');
        }

        // Total
        $total = isset($service_rating['count']) ? $service_rating['count'] : '';
        if($total != '') {
            update_field('feefo_total_reviews', $total, 'option');
        }

    }

    /**
     * Updates service ratings
     */
    public function update_product($product_id) {

        if(!$product_id || !is_numeric($product_id)) { return false; }

        $product = wc_get_product($product_id);
        $sku = $product->get_sku();

        if($sku) {
            
            $product_data = $this->get_product_rating($sku);
            $last_product_review = $this->get_product_reviews($sku, true);

            if( !empty($product_data) && is_array($product_data) ) {

                // Product rating
                $rating = isset($product_data['rating']) ? $product_data['rating'] : '';
                if($rating) {
                    update_field('product_feefo_rating', $rating, $product_id);
                }

                // Product review coujnt
                $review_count = isset($product_data['review_count']) ? $product_data['review_count'] : '';
                if($rating) {
                    update_field('product_feefo_reviews_count', $review_count, $product_id);
                }
            }

            if(!empty($last_product_review) && is_array($last_product_review)) {
                $last_product_review = reset($last_product_review);
                update_field('_product_feefo_last_review', $last_product_review, $product_id);
            }

        }

    }

    /**
     * Handles saving options and products
     */
    public function on_save_feefo($post_id) {
        
        $screen = get_current_screen();
        $post_type = get_post_type($post_id);

        if( strpos($screen->id, 'acf-apm-settings') == true ) {
            
            $this->update_service();

            // Maybe register cron
            $this->register_feefo_cron('option');
            

        } elseif($post_type === 'product') {

            $this->update_product($post_id);

            $this->register_feefo_cron($post_id);

        }

    }

    /** ----------------------------------------------------------------------------
     * Feefo Crons
     * 
     * Cron-related functions for Feefo's integration
     * -----------------------------------------------------------------------------*/

    /**
     * Register custom cron
     * 
     * @param int $post_id
     */
    public function register_feefo_cron($post_id) {
        
        // ACF Options page
        if($post_id === 'option') {

            $cron_args = array('option', 'Options page');

        } else {

            $cron_args = array($post_id, get_the_title($post_id));

        }
    
         // get random time
        $rand_hour = mt_rand(00, 24);
        $rand_hour = str_pad($rand_hour, 2, "0", STR_PAD_LEFT);
        
        $rand_min = mt_rand(00, 60);
        $rand_min = str_pad($rand_min, 2, "0", STR_PAD_LEFT);
    
        $when = strtotime($rand_hour.':'.$rand_min.' +1 week');
    
        if (!wp_next_scheduled('feefo_cron', $cron_args)) { // only create cron if it doesn't exist
    
            wp_schedule_event($when, 'weekly', 'feefo_cron', $cron_args);
    
        } else { // If it exists...
    
            // Delete current schedule.
            wp_clear_scheduled_hook('feefo_cron', $cron_args);
    
            // Create new one.
            wp_schedule_event($when, 'weekly', 'feefo_cron', $cron_args);
    
        }
    
    }

    /**
     * Callback fired by cron
     * to update Feefo ratings
     *
     * @param int $post_id
    */
    public function update_ratings_via_cron($post_id, $title) {

        if($post_id == 'option') {

            $this->update_service();

        } else {

            if(is_numeric($post_id)) {

                $post_type = get_post_type($post_id);

                if($post_type === 'product') {
                    $this->update_product($post_id);
                }

            }

        }

    }

    /** 
     * Register custom cron schedules
     * 
     * @param array $sechdules
    */
    public function add_cron_schedules( $schedules ) {
   
        $schedules['weekly'] = array(
           'interval'  => WEEK_IN_SECONDS,
           'display'   => 'Weekly'
        );

        // Test only
        // $schedules['1min'] = array(
        //     'interval'  => MINUTE_IN_SECONDS,
        //     'display'   => 'Every minute'
        // );

        return $schedules;
   
    }

}

/**
 * Helper function that displays
 * a Feefo box showing service ratings.
 */
function feefo_service_rating_box() {

    $feefo_total_average = get_field('feefo_total_average', 'option');
    $feefo_total_reviews = get_field('feefo_total_reviews', 'option');

    if($feefo_total_average && $feefo_total_reviews):

        $percentage = ($feefo_total_average * 100) / 5;
    ?>

        <div class="wp__feefo banners dark">
            <a href="https://www.feefo.com/reviews/bodyset" target="_blank">
                <div class="feefo__rating">
                    <div class="feefo__stars">
                        <div class="filled__stars" style="width: <?php echo $percentage; ?>%;">
                            <span class="stars">★★★★★</span>
                        </div>
                        <div class="empty__stars">
                            <span class="stars">★★★★★</span>
                        </div>
                    </div>

                    <div class="feefo__score">
                        <?php echo $feefo_total_average; ?>/5
                    </div>
                </div>

                <div class="feefo__meta">
                    <p>Independent rating based on <?php echo $feefo_total_reviews; ?> verified reviews</p> 
                    <figure>
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 226.8 52.8" style="enable-background:new 0 0 226.8 52.8;" xml:space="preserve"> <g> <path class="letters" style="fill:#424243;" d="M49.4,28.5h-18c0.8-3.6,4.2-6.1,8.9-6.1C44.8,22.5,48.1,24.6,49.4,28.5 M60.8,36.6v-1.2 c0-14.3-8.4-23.1-20.6-23.1c-5.2,0-10.3,2.1-14.2,6c-3.8,3.8-5.9,8.7-5.9,14.1c0,5.4,1.9,10.3,5.6,14.2c3.9,4.2,8.7,6.2,14.5,6.2 c8.6,0,15-4.1,18.7-12.1H46.6c-1.7,1.5-3.6,2.1-5.9,2.1c-4.9,0-7.9-2.1-8.9-6.2H60.8z"/> <path class="letters" style="fill:#424243;" d="M92.2,28.5h-18c0.8-3.6,4.1-6.1,8.9-6.1C87.6,22.5,90.8,24.6,92.2,28.5 M103.6,36.6v-1.2 c0-14.3-8.4-23.1-20.6-23.1c-5.2,0-10.3,2.1-14.2,6C65,22,62.9,27,62.9,32.3c0,5.4,1.9,10.3,5.7,14.2c3.9,4.2,8.7,6.2,14.5,6.2 c8.6,0,15-4.1,18.7-12.1H89.3c-1.7,1.5-3.6,2.1-5.9,2.1c-4.9,0-7.9-2.1-8.9-6.2H103.6z"/> <path class="letters" style="fill:#424243;" d="M4.1,22v29.8h11.1V22h4.5v-8.6h-4.5v-0.9c0-3.5,0.8-4.7,4.2-4.7h0.3V0.1C19.1,0.1,18.5,0,18,0 C8.5,0,4.1,3.7,4.1,11.9c0,0.4,0.1,0.9,0.1,1.4H0V22H4.1z"/> <path class="letters" style="fill:#424243;" d="M107.9,22v29.8H119V22h4.5v-8.6H119v-0.9c0-3.5,0.8-4.7,4.2-4.7h0.3V0.1c-0.6,0-1.2-0.1-1.8-0.1 c-9.4,0-13.9,3.7-13.9,11.9c0,0.4,0.1,0.9,0.1,1.4h-4.1V22H107.9z"/> <path class="letters" style="fill:#424243;" d="M152.8,32.6c0,5.2-4.1,9.4-9.1,9.4c-4.9,0-9.1-4.2-9.1-9.4c0-5.3,4.1-9.5,9.1-9.5 C148.7,23.1,152.8,27.3,152.8,32.6 M164,32.3c0-5.2-2.2-10.5-6.3-14.4c-3.9-3.6-8.7-5.6-14.2-5.6c-5.2,0-10.3,2.1-14.2,6 c-3.8,3.8-5.9,8.7-5.9,14.1c0,5.4,1.9,10.3,5.7,14.2c3.9,4.2,8.7,6.2,14.5,6.2c5.8,0,10.7-2.1,14.6-6C162.1,42.9,164,38.1,164,32.3 "/> <path style="fill:#F7DC04;" d="M181.4,26.1c-5.4-0.3-9.8-4.7-10.1-10.2h21.5h2.3v-0.3c0-9.3-6-15.6-14.2-15.6 c-8.7,0-14.6,6.4-14.6,14.7c0,7.8,6.1,14.7,14.5,14.7c6.1,0,10.9-3.4,13.5-9.6h-2.4c-1.7,3.6-5.4,6.2-9.8,6.2 C181.8,26.1,181.6,26.1,181.4,26.1"/> <path style="fill:#F7DC04;" d="M211.6,26.1c5.4-0.3,9.8-4.7,10.1-10.2h-21.5h-2.3v-0.3c0-9.3,6-15.6,14.2-15.6 c8.7,0,14.6,6.4,14.6,14.7c0,7.8-6.1,14.7-14.5,14.7c-6.1,0-10.9-3.4-13.5-9.6h2.4c1.7,3.6,5.4,6.2,9.7,6.2 C211.2,26.1,211.4,26.1,211.6,26.1"/> </g> </svg>
                    </figure>
                </div>
            </a>
        </div><!-- wp__feefo -->

    <?php
    endif;
}

/**
 * Helper function that displays
 * a Feefo box showing product ratings.
 */
function feefo_product_rating_box($product_id, $small = false) {

    $product_rating = get_field('product_feefo_rating', $product_id);

    if($product_rating):

        $percentage = ($product_rating * 100) / 5;
    ?>

        <div class="wp__feefo product dark <?php echo ($small == true ? 'small' : ''); ?>">
            <div class="feefo__rating">
                <div class="feefo__stars">
                    <div class="filled__stars" style="width: <?php echo $percentage; ?>%;">
                        <span class="stars">★★★★★</span>
                    </div>
                    <div class="empty__stars">
                        <span class="stars">★★★★★</span>
                    </div>
                </div>

                <div class="feefo__score">
                    <?php echo $product_rating; ?>/5
                </div>
            </div>

            <div class="feefo__meta">
                <figure>
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 226.8 52.8" style="enable-background:new 0 0 226.8 52.8;" xml:space="preserve"> <g> <path class="letters" style="fill:#424243;" d="M49.4,28.5h-18c0.8-3.6,4.2-6.1,8.9-6.1C44.8,22.5,48.1,24.6,49.4,28.5 M60.8,36.6v-1.2 c0-14.3-8.4-23.1-20.6-23.1c-5.2,0-10.3,2.1-14.2,6c-3.8,3.8-5.9,8.7-5.9,14.1c0,5.4,1.9,10.3,5.6,14.2c3.9,4.2,8.7,6.2,14.5,6.2 c8.6,0,15-4.1,18.7-12.1H46.6c-1.7,1.5-3.6,2.1-5.9,2.1c-4.9,0-7.9-2.1-8.9-6.2H60.8z"/> <path class="letters" style="fill:#424243;" d="M92.2,28.5h-18c0.8-3.6,4.1-6.1,8.9-6.1C87.6,22.5,90.8,24.6,92.2,28.5 M103.6,36.6v-1.2 c0-14.3-8.4-23.1-20.6-23.1c-5.2,0-10.3,2.1-14.2,6C65,22,62.9,27,62.9,32.3c0,5.4,1.9,10.3,5.7,14.2c3.9,4.2,8.7,6.2,14.5,6.2 c8.6,0,15-4.1,18.7-12.1H89.3c-1.7,1.5-3.6,2.1-5.9,2.1c-4.9,0-7.9-2.1-8.9-6.2H103.6z"/> <path class="letters" style="fill:#424243;" d="M4.1,22v29.8h11.1V22h4.5v-8.6h-4.5v-0.9c0-3.5,0.8-4.7,4.2-4.7h0.3V0.1C19.1,0.1,18.5,0,18,0 C8.5,0,4.1,3.7,4.1,11.9c0,0.4,0.1,0.9,0.1,1.4H0V22H4.1z"/> <path class="letters" style="fill:#424243;" d="M107.9,22v29.8H119V22h4.5v-8.6H119v-0.9c0-3.5,0.8-4.7,4.2-4.7h0.3V0.1c-0.6,0-1.2-0.1-1.8-0.1 c-9.4,0-13.9,3.7-13.9,11.9c0,0.4,0.1,0.9,0.1,1.4h-4.1V22H107.9z"/> <path class="letters" style="fill:#424243;" d="M152.8,32.6c0,5.2-4.1,9.4-9.1,9.4c-4.9,0-9.1-4.2-9.1-9.4c0-5.3,4.1-9.5,9.1-9.5 C148.7,23.1,152.8,27.3,152.8,32.6 M164,32.3c0-5.2-2.2-10.5-6.3-14.4c-3.9-3.6-8.7-5.6-14.2-5.6c-5.2,0-10.3,2.1-14.2,6 c-3.8,3.8-5.9,8.7-5.9,14.1c0,5.4,1.9,10.3,5.7,14.2c3.9,4.2,8.7,6.2,14.5,6.2c5.8,0,10.7-2.1,14.6-6C162.1,42.9,164,38.1,164,32.3 "/> <path style="fill:#F7DC04;" d="M181.4,26.1c-5.4-0.3-9.8-4.7-10.1-10.2h21.5h2.3v-0.3c0-9.3-6-15.6-14.2-15.6 c-8.7,0-14.6,6.4-14.6,14.7c0,7.8,6.1,14.7,14.5,14.7c6.1,0,10.9-3.4,13.5-9.6h-2.4c-1.7,3.6-5.4,6.2-9.8,6.2 C181.8,26.1,181.6,26.1,181.4,26.1"/> <path style="fill:#F7DC04;" d="M211.6,26.1c5.4-0.3,9.8-4.7,10.1-10.2h-21.5h-2.3v-0.3c0-9.3,6-15.6,14.2-15.6 c8.7,0,14.6,6.4,14.6,14.7c0,7.8-6.1,14.7-14.5,14.7c-6.1,0-10.9-3.4-13.5-9.6h2.4c1.7,3.6,5.4,6.2,9.7,6.2 C211.2,26.1,211.4,26.1,211.6,26.1"/> </g> </svg>
                </figure>
            </div>
        </div><!-- wp__feefo -->

    <?php
    endif;
}
<?php
/**
 * APM Locations template
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<section class="apm__locations has-deps" data-deps='{"js":["apm-download-xml", "apm-filters"]}' data-deps-path="apm_ajax_object" data-deps-action="apm_filter_locations">
    <div id="apm_response" class="max__width"></div>
</section><!-- apm__locations -->

<?php get_footer(); ?>
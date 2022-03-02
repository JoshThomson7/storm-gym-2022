<?php
/**
 * APM Services
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$services = new APM_Services();
$service_cats = $services->get_service_categories(array());

include get_stylesheet_directory().'/modules/inner-banner.php';
?>

<section class="apm__services has-deps" data-deps='{"js":["apm-filters"]}' data-deps-path="apm_ajax_object" data-deps-action="apm_filter_services">
    <div class="max__width">
        <div class="apm__filters">
            <form id="apm_filters">
                <article class="is-radio">
                    <input type="radio" id="filter-all" value="" name="service_cat">
                    <label for="filter-all">
                        <span>See all</span>
                    </label>
                </article>

                <?php if(!empty($service_cats)): ?>
                    <?php
                        foreach($service_cats as $service_cat):
                            
                        $checked = '';
                        if($service_cat->slug === 'core') {
                            $checked = ' checked';
                        }

                        $cat_icon = get_field('tax_icon', 'term_'.$service_cat->term_id);
                        $label_id = $service_cat->slug.'_'.$service_cat->term_id;
                    ?>
                        <article class="is-radio">
                            <input type="radio" id="<?php echo $label_id; ?>" value="<?php echo $service_cat->term_id; ?>" name="service_cat"<?php echo $checked; ?>>
                            <label for="<?php echo $label_id; ?>">
                                <i class="<?php echo $cat_icon; ?> fa-fw"></i>
                                <span><?php echo $service_cat->name; ?></span>
                            </label>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </form>
        </div>

        <div id="apm_response" class="apm__cards"></div>
    </div><!-- max__width -->
</section><!-- apm__services -->

<?php get_footer(); ?>
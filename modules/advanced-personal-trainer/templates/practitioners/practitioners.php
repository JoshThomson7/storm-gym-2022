<?php
/**
 * APM Practitioners
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$practitioners = new APM_Practitioners();
$departments = $practitioners->get_departments();

include get_stylesheet_directory().'/modules/inner-banner.php';
?>

<section class="apm__practitioners has-deps" data-deps='{"js":["apm-filters"]}' data-deps-path="apm_ajax_object" data-deps-action="apm_filter_practitioners">
    <div class="max__width">
        <div class="apm__filters">
            <form id="apm_filters">
                <article class="is-radio">
                    <input type="radio" id="filter-all" value="" name="department" checked>
                    <label for="filter-all">
                        <span>See all</span>
                    </label>
                </article>

                <?php if(!empty($departments)): ?>
                    <?php
                        foreach($departments as $department):
                        
                        $cat_icon = get_field('tax_icon', 'term_'.$department->term_id);
                        $label_id = $department->slug.'_'.$department->term_id;
                    ?>
                        <article class="is-radio">
                            <input type="radio" id="<?php echo $label_id; ?>" value="<?php echo $department->term_id; ?>" name="department">
                            <label for="<?php echo $label_id; ?>">
                                <i class="<?php echo $cat_icon; ?> fa-fw"></i>
                                <span><?php echo $department->name; ?></span>
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
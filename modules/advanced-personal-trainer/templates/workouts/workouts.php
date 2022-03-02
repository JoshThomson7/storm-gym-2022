<?php
/**
 * APT Workouts
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
AVB::avb_banners();

$workouts = new APT_Workouts();
$levels = $workouts->get_tax_terms('workout_level');
$intensities = $workouts->get_tax_terms('workout_intensity');
$good_for = $workouts->get_tax_terms('workout_good_for');
$equipment = $workouts->get_tax_terms('workout_equipment');
?>

<section class="apt-workouts has-deps" data-deps='{"js":["apt.workouts"]}' data-deps-path="apt_ajax_object" data-deps-action="apt_filter_workouts">
    <div class="max__width">
        <div class="apt-workouts--venue">
            <aside class="apt-workouts--bar apt-filters">
                <form id="apt_filters">
                    <?php if(!empty($levels)): ?>
                        <article class="is-radio">
                            <h5>Level</h5>

                            <ul>
                                <?php foreach($levels as $level): ?>
                                    <li>    
                                        <input id="<?php echo $level->slug.'_'.$level->slug; ?>" type="checkbox" name="workout_level[]" value="<?php echo $level->term_id; ?>">
                                        <label for="<?php echo $level->slug.'_'.$level->slug; ?>"><?php echo $level->name; ?></label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endif; ?>
                    
                    <?php if(!empty($intensities)): ?>
                        <article class="is-radio">
                            <h5>Intensity</h5>
                            
                            <ul>
                                <?php foreach($intensities as $intensity): ?>
                                    <li>
                                        <input id="<?php echo $intensity->slug.'_'.$intensity->slug; ?>" type="checkbox" name="workout_intensity[]" value="<?php echo $intensity->term_id; ?>">
                                        <label for="<?php echo $intensity->slug.'_'.$intensity->slug; ?>"><?php echo $intensity->name; ?></label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endif; ?>

                    <article class="is-radio">
                        <h5>Duration</h5>
                        
                        <ul>
                            <li>
                                <input id="0_100000" type="radio" name="workout_duration" value="0_100000" checked>
                                <label for="0_100000">Any duration</label>
                            </li>
                            <li>
                                <input id="duration_15" type="radio" name="workout_duration" value="1_15">
                                <label for="duration_15">15 min or less</label>
                            </li>
                            <li>
                                <input id="duration_15_30" type="radio" name="workout_duration" value="15_30">
                                <label for="duration_15_30">15&mdash;30 min</label>
                            </li>
                            <li>
                                <input id="duration_30_45" type="radio" name="workout_duration" value="30_45">
                                <label for="duration_30_45">30&mdash;45 min</label>
                            </li>
                            <li>
                                <input id="duration_45" type="radio" name="workout_duration" value="45_100000">
                                <label for="duration_45">More than 45 min</label>
                            </li>
                        </ul>
                    </article>

                    <?php if(!empty($good_for)): ?>
                        <article class="is-select">
                            <h5>Good for</h5>

                            <select name="workout_good_for[]" class="chosen-select" multiple>
                                <option value=""></option>
                                <?php foreach($good_for as $good_for_item): ?>
                                    <option value="<?php echo $good_for_item->term_id; ?>"><?php echo $good_for_item->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </article>
                    <?php endif; ?>

                    <?php if(!empty($equipment)): ?>
                        <article class="is-select">
                            <h5>Equipment</h5>

                            <select name="workout_equipment[]" class="chosen-select" multiple>
                                <option value=""></option>
                                <?php foreach($equipment as $equipment_item): ?>
                                    <option value="<?php echo $equipment_item->term_id; ?>"><?php echo $equipment_item->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </article>
                    <?php endif; ?>

                </form>
            </aside>

            <div id="apt_workouts_response" class="apt-workouts--stage cards"></div>
        </div><!-- apt-workouts--venue -->
    </div><!-- max__width -->
</section><!-- apt-workouts -->

<?php get_footer(); ?>